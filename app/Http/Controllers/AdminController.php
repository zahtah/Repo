<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
        // $userType = Auth::user()-> usertype;
        // if ($userType ==1){
        //     return view('admin.index');
        // }
        // else{
        //     return view('home.master');
        // }
    }
}
