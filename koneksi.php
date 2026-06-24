<?php

// Fungsi sederhana untuk memuat file .env jika ada
if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Abaikan komentar
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            // Pecah berdasarkan tanda '=' pertama
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                list($name, $value) = $parts;
                $name = trim($name);
                $value = trim($value);
                
                // Hapus tanda kutip jika ada
                if (preg_match('/^"(.*)"$/', $value, $matches)) {
                    $value = $matches[1];
                } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                    $value = $matches[1];
                }
                
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
                
                // Coba gunakan putenv jika fungsinya diaktifkan di server
                if (function_exists('putenv')) {
                    @putenv(sprintf('%s=%s', $name, $value));
                }
            }
        }
    }
}

// Muat file .env dari root folder project
loadEnv(__DIR__ . '/.env');

// Ambil kredensial database dengan prioritas $_ENV / $_SERVER kemudian getenv / default lokal
$db_host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? (getenv('DB_HOST') ?: 'localhost');
$db_user = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? (getenv('DB_USER') ?: 'root');
$db_pass = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
$db_name = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? (getenv('DB_NAME') ?: 'isukampus');

$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$koneksi) {
    die("Koneksi Gagal : " . mysqli_connect_error());
}

?>