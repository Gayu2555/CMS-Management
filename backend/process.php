<?php
// Prevent any output before JSON response
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require_once 'config.php';

header('Content-Type: application/json');

function generateSlug($string)
{
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
    $slug = preg_replace('/-+/', '-', $slug);
    return $slug;
}

function sanitizeString($string)
{
    // Replace deprecated FILTER_SANITIZE_STRING
    return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
}

function handleError($message)
{
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}

function generateUniqueSlug($pdo, $table, $baseSlug)
{
    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
}

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Metode request tidak valid');
    }

    $pdo = connectDB();

    // Validate required fields
    $required_fields = ['title', 'date_created', 'content', 'author', 'category', 'position'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Field '$field' harus diisi");
        }
    }

    // Sanitize and prepare data
    $title = sanitizeString($_POST['title']);
    $baseSlug = generateSlug($title);
    $category = sanitizeString($_POST['category']);
    $position = sanitizeString($_POST['position']);

    // Validate position value
    $valid_positions = ['news_list', 'sub_headline', 'headline'];
    if (!in_array($position, $valid_positions)) {
        throw new Exception('Posisi artikel tidak valid');
    }

    // Generate unique slug
    $slug = generateUniqueSlug($pdo, $category, $baseSlug);

    $date_published = sanitizeString($_POST['date_created']);
    $content = $_POST['content']; // Keep JSON as is
    $author_name = sanitizeString($_POST['author']);

    // Menangani input figcaption
    $figcaption = '';
    if (isset($_POST['figcaption'])) {
        $figcaption = sanitizeString($_POST['figcaption']);
        // Validasi panjang figcaption
        if (strlen($figcaption) > 255) {
            throw new Exception('Keterangan gambar terlalu panjang. Maksimal 255 karakter.');
        }
    }

    // Validate JSON content
    $decoded_content = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Format konten tidak valid');
    }

    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_info = pathinfo($_FILES["image"]["name"]);
        $file_extension = strtolower($file_info['extension']);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Validate image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_types)) {
            throw new Exception('Tipe file tidak valid. Hanya JPG, JPEG, PNG & GIF yang diperbolehkan.');
        }

        if ($_FILES["image"]["size"] > 2000000) {
            throw new Exception('Ukuran file terlalu besar. Maksimal 2MB.');
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            throw new Exception('Gagal mengunggah gambar.');
        }

        $image_url = 'uploads/' . $new_filename;
    } else {
        throw new Exception('Gambar harus diunggah.');
    }

    // Generate description from content
    $plain_text = '';
    foreach ($decoded_content['ops'] as $op) {
        if (isset($op['insert']) && is_string($op['insert'])) {
            $plain_text .= $op['insert'];
        }
    }
    $description = substr(trim($plain_text), 0, 200) . '...';

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Prepare article data
        $article_data = json_encode([
            'title' => $title,
            'slug' => $slug,
            'date_published' => $date_published,
            'content' => $content,
            'image_url' => $image_url,
            'figcaption' => $figcaption,
            'description' => $description,
            'author_name' => $author_name
        ]);

        // Insert article
        $sql = "INSERT INTO $category (title, slug, date_published, content, image_url, description, author_name, figcaption) 
                VALUES (:title, :slug, :date_published, :content, :image_url, :description, :author_name, :figcaption)";

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':date_published' => $date_published,
            ':content' => $article_data,
            ':image_url' => $image_url,
            ':description' => $description,
            ':author_name' => $author_name,
            ':figcaption' => $figcaption
        ]);

        if (!$success) {
            throw new Exception('Gagal menyimpan artikel ke database');
        }

        // Get the new article's ID
        $article_id = $pdo->lastInsertId();

        // Get category_id from categories table
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE TABLE_NAME = ?");
        $stmt->execute([$category]);
        $category_id = $stmt->fetchColumn();

        if (!$category_id) {
            throw new Exception('Kategori tidak ditemukan');
        }

        // Insert position data
        $stmt = $pdo->prepare("INSERT INTO article_positions (category_id, article_id, position) VALUES (?, ?, ?)");
        $success = $stmt->execute([$category_id, $article_id, $position]);

        if (!$success) {
            throw new Exception('Gagal menyimpan posisi artikel');
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Artikel berhasil dipublikasikan.'
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    handleError($e->getMessage());
}
