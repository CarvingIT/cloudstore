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
        $s = new Source();
        $s->name = $request->name;
        $s->type = $request->type;
        $s->drive_id = $request->drive_id;
        
        $details = array();
        if($request->type == 'local'){
            $details['path'] = $request->path;
            $details['target_path'] = $request->target_path;
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

        if(!empty($request->target_path)){
            $drive = Drive::find($request->drive_id);
            if($drive->type == 'GoogleDrive'){
                $dc = new GoogleDriveController($drive);
                $dir = $dc->createDirectory($request->target_path);
                $details['cloud_id'] = $dir->id;
            }
        }

        $s->details = json_encode($details);
        $s->save();
        return redirect('/admin/sources');
    }

    public function delete($source_id){
        $s = Source::find($source_id);
        $s->delete(); 
        return redirect('/admin/sources');
    }
}
