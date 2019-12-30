<?php

namespace App\Http\Controllers\Text;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurlController extends Controller
{
    //
    public function textcurl()
    {
        return view('curl.curl');
    }
}
