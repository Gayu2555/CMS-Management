<?php

if (!function_exists('loadEnv')) {
    function loadEnv($path = '.env')
    {
        if (!file_exists($path)) {
            throw new Exception('.env file tidak ditemukan di lokasi ' . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

if (!function_exists('connectDB')) {
    function connectDB()
    {
        // Memuat variabel lingkungan dari file .env
        try {
            loadEnv();
        } catch (Exception $e) {
            throw new Exception("Gagal memuat file .env: " . $e->getMessage());
        }

        // Baca variabel dari lingkungan
        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';
        $db = getenv('DB_NAME') ?: 'default_db';

        if (!$host || !$user || !$db) {
            throw new Exception('Konfigurasi database di .env tidak lengkap.');
        }

        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$db;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("Koneksi database gagal: " . $e->getMessage());
        }
    }
}

if (!function_exists('escapeHTML')) {
    function escapeHTML($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
