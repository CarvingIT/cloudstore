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

    public function listFiles($request){
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);
        if(empty($params)){
            $params = array('pageSize'=>'10',
                'orderBy'=> 'modifiedTime desc',
                'fields' => '*',
                //'q' => 'name contains "sql.gz"'
                );
        }
        $list = $service->files->listFiles($params);

        $results_data = array();
        foreach($list as $l){
            $results_data[] = array('filename'=>$l->name, 
                    'size'=>$l->size, 
                    'updated_at'=>$l->modifiedTime, 
                    'actions'=>'none');
        }
        $results = array(
            'data'=>$results_data,
            'draw'=>(int) 1,
            'recordsTotal'=> 100,
            'recordsFiltered' => 10,
            'error'=> '',
        );
        return json_encode($results);
    }

}
