<?php

use App\Encryption\SimpleEncrypter;

if (!function_exists('simple_encrypt')) {
    /**
     * เข้ารหัสข้อมูลด้วย SimpleEncrypter
     */
    function simple_encrypt($value, $serialize = true) {
        if (!class_exists('App\Encryption\SimpleEncrypter')) {
            throw new RuntimeException('SimpleEncrypter class is not available.');
        }
        
        static $encrypter = null;
        if ($encrypter === null) {
            $encrypter = new SimpleEncrypter();
        }
        
        return $encrypter->encrypt($value, $serialize);
    }
}

if (!function_exists('simple_decrypt')) {
    /**
     * ถอดรหัสข้อมูลด้วย SimpleEncrypter
     */
    function simple_decrypt($payload, $unserialize = true) {
        if (!class_exists('App\Encryption\SimpleEncrypter')) {
            throw new RuntimeException('SimpleEncrypter class is not available.');
        }
        
        static $encrypter = null;
        if ($encrypter === null) {
            $encrypter = new SimpleEncrypter();
        }
        
        return $encrypter->decrypt($payload, $unserialize);
    }
}

// แทนที่ฟังก์ชัน encrypt และ decrypt ของ Laravel
if (!function_exists('encrypt')) {
    /**
     * เข้ารหัสข้อมูล (แทนที่ฟังก์ชัน encrypt เดิม)
     */
    function encrypt($value, $serialize = true) {
        return simple_encrypt($value, $serialize);
    }
}

if (!function_exists('decrypt')) {
    /**
     * ถอดรหัสข้อมูล (แทนที่ฟังก์ชัน decrypt เดิม)
     */
    function decrypt($payload, $unserialize = true) {
        return simple_decrypt($payload, $unserialize);
    }
}