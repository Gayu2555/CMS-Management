<?php
// process.php
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

try {
    $pdo = connectDB();
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'saveArticle':
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

            // Handle image upload
            $imageUrl = handleImageUpload();

            // Prepare article data
            $articleData = [
                'category_id' => $_POST['category'],
                'title' => $_POST['title'],
                'slug' => generateSlug($_POST['title']),
                'date_published' => $_POST['date_created'],
                'content' => $_POST['content'],
                'image_url' => $imageUrl,
                'author_name' => $_POST['author'],
                'description' => substr(strip_tags($_POST['content']), 0, 200) . '...'
            ];

            // Insert article
            $sql = "INSERT INTO articles 
                    (category_id, title, slug, date_published, content, image_url, description, author_name) 
                    VALUES 
                    (:category_id, :title, :slug, :date_published, :content, :image_url, :description, :author_name)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($articleData);

            echo json_encode([
                'success' => true,
                'message' => 'Article saved successfully',
                'articleId' => $pdo->lastInsertId()
            ]);
            break;

        case 'getCategories':
            $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true,
                'categories' => $categories
            ]);
            break;

        case 'getArticles':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            // Get total count
            $total = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();

            // Get articles with category information
            $sql = "SELECT a.*, c.name as category_name 
                    FROM articles a 
                    JOIN categories c ON a.category_id = c.id 
                    ORDER BY a.date_published DESC 
                    LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'articles' => $articles,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]);
            break;

        case 'deleteArticle':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $articleId = $_POST['article_id'] ?? null;
            if (!$articleId) {
                throw new Exception('Article ID is required');
            }

            // Get image URL before deletion
            $stmt = $pdo->prepare("SELECT image_url FROM articles WHERE id = ?");
            $stmt->execute([$articleId]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            // Delete article
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$articleId]);

            // Delete associated image
            if ($article['image_url']) {
                $imagePath = "../uploads/" . $article['image_url'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Article deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
