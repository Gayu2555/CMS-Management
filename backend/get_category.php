<?php
require 'config.php';

// Fungsi untuk mendapatkan kategori
function getCategories()
{
    try {
        $pdo = connectDB();
        $query = $pdo->query("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = 'urbansiana_id'  -- gunakan string literal langsung
            AND TABLE_NAME IN ('international', 'japanese', 'kesehatan', 'konflik', 'kpop', 'national', 'politics', 'teknologi', 'regional')
        ");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting categories: " . $e->getMessage());  // tambahkan logging
        return [];
    }
}

$categories = getCategories();

// Proses form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validasi input
        if (empty($_POST['category'])) {
            throw new Exception("Kategori harus dipilih");
        }

        // Proses penyimpanan artikel di sini
        // ...

        $success = "Artikel berhasil dipublikasikan!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
