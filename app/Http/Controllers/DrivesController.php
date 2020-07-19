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

}
