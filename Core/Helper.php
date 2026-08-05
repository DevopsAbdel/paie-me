<?php

namespace Core;

class Helper
{
    public static function asset(string $path): string
    {
        return '/paie-me/assets/' . ltrim($path, '/');
    }

    public static function url(string $path = ''): string
    {
        return '/paie-me/' . ltrim($path, '/');
    }

    public static function csrf(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('_csrf_token', $token);
        return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
    }

    public static function verifyCsrf(string $token): bool
    {
        $stored = Session::get('_csrf_token');
        Session::remove('_csrf_token');
        return $token === $stored;
    }

    public static function truncate(string $text, int $length = 50): string
    {
        return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
    }

    public static function logoWebPath(?string $logo): string
    {
        return $logo ? '/paie-me/' . ltrim($logo, '/') : '';
    }

    public static function logoFilePath(?string $logo): string
    {
        return $logo ? __DIR__ . '/../' . ltrim($logo, '/') : '';
    }

    public static function logoDataUri(?string $logo): string
    {
        $file = self::logoFilePath($logo);
        if (!$file || !is_file($file)) return '';
        $mime = mime_content_type($file) ?: 'image/png';
        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($file));
    }
}
