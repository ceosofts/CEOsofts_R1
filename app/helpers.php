<?php

use App\Encryption\CustomEncrypter;

/**
 * Helper functions แทนที่ encrypt() และ decrypt() ของ Laravel
 * เพื่อแก้ปัญหา "Could not encrypt the data"
 */

if (!function_exists('encrypt')) {
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     */
    function encrypt($value, $serialize = true)
    {
        static $encrypter = null;
        
        if ($encrypter === null) {
            $encrypter = new CustomEncrypter();
        }
        
        return $encrypter->encrypt($value, $serialize);
    }
}

if (!function_exists('decrypt')) {
    /**
     * Decrypt the given value.
     *
     * @param  string  $value
     * @param  bool  $unserialize
     * @return mixed
     */
    function decrypt($value, $unserialize = true)
    {
        static $encrypter = null;
        
        if ($encrypter === null) {
            $encrypter = new CustomEncrypter();
        }
        
        return $encrypter->decrypt($value, $unserialize);
    }
}