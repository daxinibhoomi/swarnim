<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers;

class TestController extends Controller
{

    public function encrypt(){

        $passphrase = "12345678";
        $plain_text = array(
            array("name" => array(
                "first_name" => "Raj",
                "last_name" => "Raj",
            )),
            array(
                "phone" => "1234567890",
            )
        );

        $encryptedData = Helpers::CryptoJSAesEncrypt($passphrase,$plain_text);
        return response($encryptedData);
    }

    public function decrypt(Request $request){

        $arrInput = $request->all();
        $decryptedData = Helpers::CryptoJSAesDecrypt($arrInput);
        return response($decryptedData);
    }
} 
    

