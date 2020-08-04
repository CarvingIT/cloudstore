<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Source;
use App\Drive;

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
        $credentials = array();
        $credentials['path'] = $request->path;
        $s->credentials = json_encode($credentials);
        $s->drive_id = $request->drive_id;
        $s->save();
        return redirect('/admin/sources');
    }

    public function delete($source_id){
        $s = Source::find($source_id);
        $s->delete(); 
        return redirect('/admin/sources');
    }
}
