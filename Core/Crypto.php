<?php

namespace Core;

class Crypto
{
    private static string $cipher = 'aes-256-cbc';

    public static function key(): string
    {
        $key = getenv('PAIE_ME_ENCRYPTION_KEY') ?: '';
        if ($key === '') {
            $config = require __DIR__ . '/../config/app.php';
            $key = $config['encryption_key'] ?? '';
        }
        if (strlen($key) !== 32) {
            $key = hash('sha256', $key ?: 'paie-me-default-key-2025!', true);
        }
        return $key;
    }

    public static function encrypt(?string $plaintext): ?string
    {
        if ($plaintext === null || $plaintext === '') return $plaintext;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$cipher));
        $encrypted = openssl_encrypt($plaintext, self::$cipher, self::key(), 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt(?string $ciphertext): ?string
    {
        if ($ciphertext === null || $ciphertext === '') return $ciphertext;
        $data = base64_decode($ciphertext);
        $ivLen = openssl_cipher_iv_length(self::$cipher);
        $iv = substr($data, 0, $ivLen);
        $encrypted = substr($data, $ivLen);
        return openssl_decrypt($encrypted, self::$cipher, self::key(), 0, $iv);
    }

    /**
     * Déchiffre sans erreur si la valeur est réellement chiffrée ; sinon retourne
     * la valeur telle quelle (données en clair, ex: seed de démo). Best-effort.
     */
    public static function tryDecrypt(?string $value): ?string
    {
        if ($value === null || $value === '') return $value;
        $data = base64_decode($value, true);
        if ($data === false) return $value;
        $ivLen = openssl_cipher_iv_length(self::$cipher);
        if (strlen($data) < $ivLen + 1) return $value;
        $result = @openssl_decrypt(substr($data, $ivLen), self::$cipher, self::key(), 0, substr($data, 0, $ivLen));
        return $result === false ? $value : $result;
    }
}
