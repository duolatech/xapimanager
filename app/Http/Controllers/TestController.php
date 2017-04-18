<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\UserModel;
use Input, Validator, Session, Captcha, Log;

class TestController extends Controller
{
    
    public function index()
    {
        return view('test');
    }
}
