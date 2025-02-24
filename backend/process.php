<?php
// process_insert.php
require_once 'config.php';

// Set header for JSON response
header('Content-Type: application/json');

// Helper functions
function generateSlug($text)
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function handleImageUpload()
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        throw new Exception('No image uploaded or upload error');
    }

    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG and PNG allowed.');
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('File too large. Maximum size is 2MB.');
    }

    $uploadDir = '../uploads/';
    $filename = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to move uploaded file.');
    }

    return $filename;
}

function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate required fields
    $requiredFields = ['title', 'content', 'category', 'author', 'date_created'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    $pdo = connectDB();

    // Sanitize input data
    $category = (int) $_POST['category'];
    $title = sanitizeInput($_POST['title']);
    $slug = generateSlug($title);
    $date_published = sanitizeInput($_POST['date_created']);
    $content = sanitizeInput($_POST['content']);
    $author_name = sanitizeInput($_POST['author']);
    $description = substr(strip_tags($content), 0, 200) . '...';

    // Handle image upload
    $imageUrl = handleImageUpload();

    // Insert article
    $sql = "INSERT INTO articles 
            (category_id, title, slug, date_published, content, image_url, description, author_name) 
            VALUES 
            (:category_id, :title, :slug, :date_published, :content, :image_url, :description, :author_name)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':category_id', $category, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    $stmt->bindParam(':date_published', $date_published, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':image_url', $imageUrl, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':author_name', $author_name, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Article saved successfully',
        'articleId' => $pdo->lastInsertId()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
