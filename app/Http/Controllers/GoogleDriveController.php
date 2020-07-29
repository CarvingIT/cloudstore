<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Google_Service_Drive;
use Google_Client;

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
        $filename = 'google-service-account_'.$this->drive->id;
        Storage::disk('private')->put($filename, $this->drive->credentials);
        $path = Storage::disk('private')->getDriver()->getAdapter()->getPathPrefix();
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$path.$filename);
        $client->useApplicationDefaultCredentials();
        $client->setScopes($this->scopes);
        return $client;
    }

    public function listFiles(Request $request){
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);
        $columns = array('name', 'quotaBytesUsed', 'modifiedTime', 'actions');

        $sort_column = 'modifiedTime';
        $sort_dir = 'desc';

        $sort_column = $columns[$request->order[0]['column']];
        $sort_dir = $request->order[0]['dir'];

        $params = array('pageSize'=>$request->length,
            'orderBy'=> "$sort_column $sort_dir",
            'fields' => '*',
            );
        if($request->start != 0){
            if($request->start > $request->session()->get('start')){
                $params['pageToken'] = $request->session()->get('next_page_token');
            }
            else{
                $params['pageToken'] = $request->session()->get('previous_page_token');
            }
        }
    
        if(!empty($request->search['value'])){
            $params['q'] = 'name contains "'.$request->search['value'].'"';
        }
        if(!empty($params['pageToken'])){
            $request->session()->put('previous_page_token', $params['pageToken']);
        }
        $request->session()->put('start', $request->start);
        $list = $service->files->listFiles($params);
        $request->session()->put('next_page_token', $list->nextPageToken);

        $results_data = array();
        foreach($list as $l){
            $results_data[] = array('filename'=>$l->name, 
                    'size'=>$l->size, 
                    'updated_at'=>$l->modifiedTime, 
                    'actions'=>'none');
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
            /*
            'recordsTotal'=> 'unknown',
            'recordsFiltered' => 'unknown',
            */
            /*
            'error'=> '',
            */
        );
        return json_encode($results);
    }

}
