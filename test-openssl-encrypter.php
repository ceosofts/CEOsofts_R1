<?php

/**
 * Test simple OpenSSL encryption directly, bypassing Laravel's Encrypter
 * to confirm if the underlying encryption functionality works
 */

echo "===== OpenSSL Direct Encryption Test =====\n\n";

// 1. Get key from .env file
echo "1. Reading APP_KEY from .env file...\n";
$envPath = __DIR__ . '/.env';
$key = null;

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/APP_KEY=base64:([^\s\n]+)/', $envContent, $matches)) {
        $encodedKey = $matches[1];
        $key = base64_decode($encodedKey);
        $keyLength = strlen($key);
        
        echo "   Found APP_KEY in .env\n";
        echo "   Key length after base64 decode: $keyLength bytes\n";
        echo "   Key (hex): " . bin2hex($key) . "\n";
        
        if ($keyLength !== 32) {
            echo "⚠️  Warning: Key length is not 32 bytes (required for AES-256-CBC)\n";
        }
    } else {
        echo "❌ Could not find APP_KEY in .env file or it's not in base64: format\n";
        
        // Generate a new key
        echo "   Generating a new random key...\n";
        $key = random_bytes(32);
        echo "   Generated key (hex): " . bin2hex($key) . "\n";
    }
} else {
    echo "❌ .env file not found\n";
    echo "   Generating a new random key...\n";
    $key = random_bytes(32);
}

// 2. Test OpenSSL directly
echo "\n2. Testing OpenSSL encryption functions...\n";
$plaintext = "This is a test string for encryption";
echo "   Plaintext: $plaintext\n";

// Generate IV
$cipher = "aes-256-cbc";
$ivlen = openssl_cipher_iv_length($cipher);
$iv = openssl_random_pseudo_bytes($ivlen);

echo "   Cipher: $cipher\n";
echo "   IV length: $ivlen bytes\n";
echo "   IV (hex): " . bin2hex($iv) . "\n";

// Encrypt
echo "   Encrypting data...\n";
$encrypted = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);

if ($encrypted === false) {
    echo "❌ Encryption failed: " . openssl_error_string() . "\n";
    exit(1);
}

echo "   Encrypted (base64): $encrypted\n";

// Create a complete payload (similar to Laravel's)
$mac = hash_hmac('sha256', $encrypted, $key);
$payload = json_encode([
    'iv' => base64_encode($iv),
    'value' => $encrypted,
    'mac' => $mac
]);
$finalPayload = base64_encode($payload);

echo "   Complete payload (base64+json): " . substr($finalPayload, 0, 64) . "...\n";

// Decrypt
echo "\n3. Testing decryption...\n";

// Decode the payload
$decoded = json_decode(base64_decode($finalPayload), true);
$decryptIv = base64_decode($decoded['iv']);
$decryptValue = $decoded['value'];
$decryptMac = $decoded['mac'];

// Verify MAC
$calculatedMac = hash_hmac('sha256', $decryptValue, $key);
if (!hash_equals($calculatedMac, $decryptMac)) {
    echo "❌ MAC verification failed\n";
    exit(1);
}

echo "   MAC verification passed\n";

// Decrypt
$decrypted = openssl_decrypt($decryptValue, $cipher, $key, 0, $decryptIv);

if ($decrypted === false) {
    echo "❌ Decryption failed: " . openssl_error_string() . "\n";
    exit(1);
}

echo "   Decrypted: $decrypted\n";

// Verify the result
if ($decrypted === $plaintext) {
    echo "✅ Test PASSED: Decrypted text matches original plaintext\n";
} else {
    echo "❌ Test FAILED: Decrypted text does not match original plaintext\n";
}

echo "\n===== System Information =====\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "OpenSSL Version: " . OPENSSL_VERSION_TEXT . "\n";
echo "OpenSSL Supports aes-256-cbc: " . (in_array('aes-256-cbc', openssl_get_cipher_methods()) ? "Yes" : "No") . "\n";

echo "\n===== Conclusion =====\n";
if ($decrypted === $plaintext) {
    echo "Basic OpenSSL encryption/decryption is working correctly on this system.\n";
    echo "If Laravel's encryption is still failing, the issue is likely in Laravel's implementation.\n";
} else {
    echo "There appears to be an issue with OpenSSL encryption on this system.\n";
    echo "This could explain why Laravel's encryption is also failing.\n";
}
