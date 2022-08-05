<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers;

class TestController extends Controller
{
    //
    public function test(Request $request){
        $message = array("H" => "Hello"); 
        $key='1234567890';
        $data = Helpers::encrypt($message,$key);
        return $data;
    }

    public function testDecrypt(Request $request){
        $arrInput = $request->all();
        $message = "U2FsdGVkX19LTHzJrY1OFCRFArMIqWeD0RFmn4MmzQ0=";
        // $key= base64_decode($arrInput["headers"]["app_id"]);
        $key = "1234567890";
        $data = Helpers::decrypt($message,$key);
        return $data;
    }
}
