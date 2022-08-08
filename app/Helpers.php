<?php
namespace App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Helpers
{
   //Encrypt
   public static function CryptoJSAesEncrypt($passphrase,$plain_text){
    try{
        //generate random salt & iv
        $salt = openssl_random_pseudo_bytes(256);
        $iv = openssl_random_pseudo_bytes(16);
    
        $iterations = 999; 
        //generate random key 
        $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 512);
    
        //encrypt plaintext data
        $encrypted_data = openssl_encrypt(json_encode($plain_text), 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);
        
        //create body object
        $data = [];
        $data["body"]["ciphertext"] = base64_encode($encrypted_data);
        $data["body"]["iv"] = bin2hex($iv);
        $data["body"]["salt"] = bin2hex($salt);

        //json encode the data and convert into base64
        $data = base64_encode(json_encode($data["body"]));
        
        //convert userkey into base64
        $headers = base64_encode($passphrase);
        $finalData = array("headers" => array("app_id" => $headers), "body" => $data);
        return $finalData;
    }catch (\Throwable $th) {
        Log::info("[ERROR][Helpers]" . $th);
        return response()->json(['error' => 'something went wrong'], 500);
    }
}

    //Decrypt
    public static function CryptoJSAesDecrypt($incomingData){
       try{
            $arrInput = $incomingData;
            //base64 decode body data
            $body = base64_decode($arrInput["body"]);
           
            //json decode body data
            $body = json_decode($body);

            $passphrase = $arrInput["headers"];
            //bade64 decode app_id
            $passphrase = base64_decode($passphrase["app_id"]);

            $salt = hex2bin($body->salt);
            $iv  = hex2bin($body->iv);        

            //bade64 decode ciphertext
            $ciphertext = base64_decode($body->ciphertext);
            
            $iterations = 999; 

            //generate random key 
            $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

            //decrypt incoming ciphertext
            $decrypted= openssl_decrypt($ciphertext , 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);

            if( $decrypted == NULL ||  $decrypted == ""){
                return response()->json(['error' => 'Invalid key'], 400);
            }else{
                if($passphrase == $decrypted["header"]["user_key"]){
                    return response()->json(['data' => $decrypted], 200);
                }else{
                    return response()->json(['error' => 'Invalid key'], 400);
                }
            }

           
       }catch (\Throwable $th) {
            Log::info("[ERROR][Helpers]" . $th);
            return response()->json(['error' => 'something went wrong'], 500);
        } 
    }

    //generate user_key
    public static function genearteRandomNumber(){
        try{
            $random = Str::random(8);
            return $random;
        }catch (\Throwable $th) {
            Log::info("[ERROR][Helpers]" . $th);
            return response()->json(['error' => 'something went wrong'], 500);
        } 
    }
}
?>