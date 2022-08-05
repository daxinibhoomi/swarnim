<?php
namespace App;

class Helpers
{
    const METHOD = 'aes-256-ctr';

    /**
     * Encrypts (but does not authenticate) a message
     * 
     * @param string $message - plaintext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encode - set to TRUE to return a base64-encoded 
     * @return string (raw binary)
     */
    public static function encrypt($message, $key, $encode = true)
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $ciphertext = openssl_encrypt(
            json_encode($message),
            self::METHOD,
            $key,
            0,
            $nonce
        );

        // Now let's pack the IV and the ciphertext together
        // Naively, we can just concatenate
        if ($encode) {
            return base64_encode($nonce.$ciphertext);
        }
        
        return $nonce.$ciphertext;
    }

    /**
     * Decrypts (but does not verify) a message
     * 
     * @param string $message - ciphertext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encoded - are we expecting an encoded string?
     * @return string
     */
    public static function decrypt($message, $key, $encoded = true)
    {
        if ($encoded) {
            // $message = base64_decode($message, true);
            $message = $message;
            if ($message === false) {
                throw new Exception('Encryption failure');
            }
        }

        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        echo $nonceSize;
        echo "---------------";
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        echo $nonce;
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');
        echo "---------------";
        echo $ciphertext;

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $key,
            0,
            $nonce,
        );

        $plaintext = json_decode($plaintext);

        dd($plaintext);

        if(!is_array($plaintext) && !is_object($plaintext)){
            $plaintext = "";
        }

        return $plaintext;
    }

    // public static function checkValidKey($data)
    // {
    //     $app_id = base64_decode($data["headers"]["app_id"]);
    //     $user_key = $data["body"]["headers"]["user_key"];

    //     $checkValidKey = true;
    //     if($app_id != $user_key){
    //         $checkValidKey = false;
    //     }
    //     return $checkValidKey;
    // }
}
?>