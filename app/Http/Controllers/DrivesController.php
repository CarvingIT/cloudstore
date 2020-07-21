<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Drive;

class DrivesController extends Controller
{
    public function drives(Request $request){
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

    public function selectDrive(Request $request){
        $drives = Drive::all();
        return view('select-drive', ['drives'=>$drives->keyBy('id')]);
    }

    public function setDrive(Request $request){
        $user_id = \Auth::user()->id;
        $user_settings = \Auth::user()->settings->keyBy('key');
        if(empty($user_settings['current_drive'])){
            $setting = new \App\UserSettings();
            $setting->user_id = \Auth::user()->id;
            $setting->key = 'current_drive';
        }
        else{
            $setting = \App\UserSettings::find($user_settings['current_drive']->id);
        }
        $setting->value = $request->drive;
        $setting->save(); 
        return redirect('home');
    }

}
