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
        //$this->setMemory($this->credentials);
        $filename = 'google-service-account_'.$this->drive->id;
        Storage::disk('private')->put($filename, $this->drive->credentials);
        $path = Storage::disk('private')->getDriver()->getAdapter()->getPathPrefix();
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$path.$filename);
        $client->useApplicationDefaultCredentials();
        $client->setScopes($this->scopes);
        return $client;
    }

    /*
    private function setMemory($text){
        $file_handle = fopen('php://memory', 'w+'); 
        fwrite($file_handle, $text);
        fclose($file_handle);
        //rewind($file_handle);
    }
    */

    public function listFiles($params = null){
        $client = $this->getClient();
        $service = new Google_Service_Drive($client);
        if(!$params){
            $params = array('pageSize'=>'100',
                'orderBy'=> 'modifiedTime desc',
                'fields' => '*',
                //'q' => 'name contains "sql.gz"'
                );
        }
        $list = $service->files->listFiles($params);
        return $list;
    }

}
