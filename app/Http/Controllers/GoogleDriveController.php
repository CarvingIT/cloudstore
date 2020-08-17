<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Google_Client;
use App\Helper;

class GoogleDriveController extends Controller
{
    private $drive;
    private $scopes;
    public $appName;

    public function __construct($drive)
    {
        $this->drive = $drive;
        $this->appName = env('APP_NAME');
        $this->scopes = implode(' ', array(Google_Service_Drive::DRIVE));
        $this->middleware('auth');
    }

    private function getClient() {
        $client = new Google_Client();
        $client->setApplicationName($this->appName);
        $process_user = posix_getpwuid(posix_geteuid());
        $filename = $process_user['name'].'_google-service-account_'.$this->drive->id;
        if (!Storage::disk('private')->has($filename)){
            Storage::disk('private')->put($filename, $this->drive->credentials);
        }
        $path = Storage::disk('private')->getDriver()->getAdapter()->getPathPrefix();
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$path.$filename);
        $client->useApplicationDefaultCredentials();
        $client->setScopes($this->scopes);
        return $client;
    }

    public function upload($path, $minus_path, $parent_id=null){ 
        $client = $this->getClient();

        $service = new Google_Service_Drive($client);
        $cloud_filename = $path;
        $path_reg = '#'.$minus_path.'/#';

        $cloud_filename = preg_replace($path_reg,'',$cloud_filename);
        if(!empty($parent_id)){
            $meta_data_args = array('name'=>$cloud_filename, 'parents'=>array($parent_id));
        }
        else{
            $meta_data_args = array('name'=>$cloud_filename);
        }
        $fileMetadata = new Google_Service_Drive_DriveFile($meta_data_args);
        $content = file_get_contents($path);
        $file = $service->files->create($fileMetadata, array(
         'data'       => $content,
         'mimeType'   => mime_content_type($path), 
         'uploadType' => 'multipart',
         'fields'     => 'id')
        );
        return $file;
    }

    public function createDirectory($dir_name){
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);
        $fileMetadata = new Google_Service_Drive_DriveFile(
                array('name' => $dir_name,
                    'mimeType'=>"application/vnd.google-apps.folder")
                );
        $file = $service->files->create($fileMetadata);
        return $file;
    }

    public function deleteFile(Request $request, $drive_id, $file_id){
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);
        try {
            $service->files->delete($file_id);
        } catch (Exception $e) {
            //print "An error occurred: " . $e->getMessage();
            return false;
        }
        return redirect()->back();
    }

    public function shareFile(Request $request, $drive_id){
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);

        $this->insertPermission($request->file_id, $request->email, 'user', 'reader');
        return redirect('/browse-drive/'.$drive_id);
    }

    public  function insertPermission($fileId, $email, $type, $role) {
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);
        $newPermission = new Google_Service_Drive_Permission();
        $newPermission->setEmailAddress($email);
        $newPermission->setType($type);
        $newPermission->setRole($role);
        try {
            return $service->permissions->create($fileId, $newPermission);
        } catch (Exception $e) {
            //print "An error occurred: " . $e->getMessage();
        }
        return NULL;
    }

    public function listFiles(Request $request, $drive_id, $folder_id=null){
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);

        $drive_info = $service->files->get('root');
        $columns = array('name', 'quotaBytesUsed', 'modifiedTime', 'actions');

        $column_num = empty($request->order[0]['column'])?2:$request->order[0]['column'];
        $sort_column = empty($columns[$column_num])?
                'modifiedTime':$columns[$column_num];
        $sort_dir = empty($request->order[0]['dir'])?'desc': $request->order[0]['dir'];

        $params = array('pageSize'=>$request->length,
            'orderBy'=> "$sort_column $sort_dir",
            'fields' => '*',
            );
        if($request->start != 0){
            if($request->start > $request->session()->get('start')){
                $params['pageToken'] = $request->session()->get('next_page_token');
            }
            else if($request->start == $request->session()->get('start')){
                $params['pageToken'] = $request->session()->get('current_page_token');
            }
            else{
                $params['pageToken'] = $request->session()->get('previous_page_token');
            }
        }
        if(empty($folder_id)) $folder_id = $drive_info->id;
        $params['q'] = "'".$folder_id."' in parents ";
    
        if(!empty($request->search['value'])){
            $params['q'] .= 'and name contains "'.$request->search['value'].'"';
        }
        if(!empty($params['pageToken'])){
            $request->session()->put('previous_page_token', $request->session()->get('current_page_token'));
            $request->session()->put('current_page_token', $params['pageToken']);
        }
        $request->session()->put('start', $request->start);
        $list = $service->files->listFiles($params);
        $request->session()->put('next_page_token', $list->nextPageToken);

        $results_data = array();
        foreach($list as $l){
            $actions = '';
            $filename = $l->name;
            if($l->mimeType == 'application/vnd.google-apps.folder'){
                $actions .= '<a title="Go" href="/browse-drive/'.$drive_id.'/'.$l->id.'"><span class="ui-icon ui-icon-extlink"></span></a>';
                $filename .= '/';
            } 
            $actions .= '<a class="sharefile" onclick="openShareDialog(\''.$l->name.'\', \''.$l->id.'\');" title="share"><span class="ui-icon ui-icon-mail-closed"></span></a>';
            if(\Auth::user()->hasRole('admin')){
                $actions .= '<a title="delete" href="/drive/'.$drive_id.'/delete-file/'.$l->id.'"><span class="ui-icon ui-icon-trash"></span></a>';
            }
            $results_data[] = array('filename'=>$filename, 
                    'size'=>$l->size, 
                    'updated_at'=>$l->modifiedTime, 
                    'actions'=>$actions);
        }
        $total_records = (int)$request->length;
        $filtered_records = (int)$request->length;
        if(!empty($list->nextPageToken)){
            $total_records += 10000;
            $filtered_records += 10000;
        }
        $results = array(
            'data'=>$results_data,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_records,
            'recordsFiltered' => $filtered_records,
        );
        return json_encode($results);
    }
}
