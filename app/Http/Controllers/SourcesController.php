<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Source;
use App\Drive;
use App\Http\Controllers\GoogleDriveController;

class SourcesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        $sources = Source::all();
        return view('sources', ['sources'=>$sources, 'drives'=>Drive::all()]);
    }

    public function save(Request $request){
        if(!$this->validateSourceInfo($request)){
            $request->session()->flash('alert-danger', 'Please choose a unique name for the source.');
            return redirect('/admin/sources');
        }
        $s = new Source();
        $s->name = $request->name;
        $s->type = $request->type;
        $s->drive_id = $request->drive_id;
        
        $details = array();
        if($request->type == 'local'){
            $details['path'] = $request->path;
        }
        else if($request->type == 'ssh'){
            $details['path'] = $request->pathssh;
            $details['server'] = $request->serverssh;
            $details['port'] = empty($request->portssh)? '22' : $request->portssh;
            $details['username'] = $request->usernamessh;
            $details['password'] = $request->passwordssh;
        }
        else if($request->type == 'ftp'){
            $details['path'] = $request->pathftp;
            $details['server'] = $request->serverftp;
            $details['port'] = empty($request->portftp)? '21' : $request->portftp;
            $details['username'] = $request->usernameftp;
            $details['password'] = $request->passwordftp;
        }
        else{}

        // There should be an option to leave this blank (root of the drive)
        // Following code sets a value for the directory-name on the drive
        $details['target_path'] = $request->name; 

        if(!empty($details['target_path'])){
            $drive = Drive::find($request->drive_id);
            if($drive->type == 'GoogleDrive'){
                $dc = new GoogleDriveController($drive);
                $dir = $dc->createDirectory($details['target_path']);
                $details['cloud_id'] = $dir->id;
            }
        }

        $s->details = json_encode($details);
        try{
            $s->save();
            return redirect('/admin/sources');
        }
        catch(\Exception $e){
            return redirect('/admin/sources')->withErrors(['msg', 'There was some error!']);
        }
    }

    public function validateSourceInfo(Request $request){
        $source = Source::where('name',$request->name)->first();
        if($source){
            return false;
        }
        return true;
    }

    public function delete($source_id){
        $s = Source::find($source_id);
        $s->delete(); 
        return redirect('/admin/sources');
    }
}
