<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Drive;
use App\Http\Controllers\GoogleDriveController;

class DrivesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        $drives = Drive::all();
        return view('drives', ['drives'=>$drives]);
    }

    public function save(Request $request){
        $d = new Drive();
        $d->name = $request->name;
        $d->credentials = $request->creds;
        $d->type = $request->type;
        $d->save();
        return redirect('/admin/drives');
    }

    public function delete($drive_id){
        $d = Drive::find($drive_id);
        $d->delete(); 
        return redirect('/admin/drives');
    }

    public function deleteFile(Request $request, $drive_id, $file_id){
        $cloud_controller_instance = $this->getCloudController($drive_id);
        return $cloud_controller_instance->deleteFile($request, $drive_id, $file_id);
    }

    public function listFiles(Request $request, $drive_id){
        $cloud_controller_instance = $this->getCloudController($drive_id);
        $list = $cloud_controller_instance->listFiles($request, $drive_id);
        echo $list;
    }

    private function getCloudController($drive_id){
        $drive = Drive::find($drive_id);
        if($drive->type == 'GoogleDrive'){
            $cloud_controller_instance = new GoogleDriveController($drive);
            return $cloud_controller_instance;
        }
        else{
            return false;
        }
    }

    public function browse($drive_id){
        return view('browse-drive', ['drive'=>\App\Drive::find($drive_id)]);
    }
}
