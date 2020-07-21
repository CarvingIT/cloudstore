<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $settings = \Auth::user()->settings->keyBy('key');
        if(empty($settings['current_drive']->value)){
            return redirect('/select-drive');
        }
        return view('home', ['drives'=>\App\Drive::all()]);
    }
}
