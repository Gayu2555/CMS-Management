<?php
// Include configuration file
require_once 'backend/config.php';

// Initialize database connection
try {
    $pdo = connectDB();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Initialize variables
$title = '';
$content = '';
$category_id = '';
$status = 'draft';
$editMode = false;
$articleId = 0;
$message = '';
$error = '';

// Check if we're in edit mode
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editMode = true;
    $articleId = (int)$_GET['edit'];

    // Fetch article data
    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$articleId]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            $title = $article['title'];
            $content = $article['content'];
            $category_id = $article['category_id'];
            $status = $article['status'];
        } else {
            $error = "Artikel tidak ditemukan!";
            $editMode = false;
        }
    } catch (PDOException $e) {
        $error = "Gagal mengambil data artikel: " . $e->getMessage();
        $editMode = false;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $status = $_POST['status'] ?? 'draft';

    // Validate inputs
    if (empty($title)) {
        $error = "Judul artikel harus diisi!";
    } elseif (empty($content)) {
        $error = "Konten artikel harus diisi!";
    } elseif (empty($category_id)) {
        $error = "Kategori harus dipilih!";
    } else {
        try {
            if ($editMode) {
                // Update existing article
                $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, category_id = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$title, $content, $category_id, $status, $articleId]);
                $message = "Artikel berhasil diperbarui!";
            } else {
                // Insert new article
                $stmt = $pdo->prepare("INSERT INTO articles (title, content, category_id, status, publication_date, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())");
                $stmt->execute([$title, $content, $category_id, $status]);
                $message = "Artikel berhasil disimpan!";

                // Clear form after successful submission if not editing
                $title = '';
                $content = '';
                $category_id = '';
                $status = 'draft';
            }
        } catch (PDOException $e) {
            $error = "Gagal menyimpan artikel: " . $e->getMessage();
        }
    }
}

// Fetch categories for dropdown
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil kategori: " . $e->getMessage();
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="id-ID">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d388dc6636.js" crossorigin="anonymous"></script>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <title><?php echo $editMode ? 'Edit Artikel' : 'Tulis Artikel Baru'; ?> - Urbansiana CMS</title>
</head>

<body class="bg-gray-100">
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-xl">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent">Urbansiana - CMS</h2>
            <p class="text-sm text-gray-400 mt-1">Sistem Manajemen Konten</p>
        </div>
        <nav class="mt-6 space-y-2 px-3">
            <a href="cms.php" class="flex items-center px-3 py-3 rounded-lg text-white bg-gray-800 border-l-4 border-blue-500 group">
                <i class="fas fa-pen-to-square mr-3 text-blue-400"></i>
                Tulis artikel
            </a>
            <a href="all_article.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-newspaper mr-3 text-gray-400 group-hover:text-blue-400"></i>
                Semua Artikel
            </a>
            <a href="category.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-folder mr-3 text-blue-400"></i>
                Kategori
            </a>
            <a href="list_reporter.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fa-solid fa-binoculars  text-blue-400 group-hover:text-blue-800"></i>
                From Reporter Lapangan
            </a>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="ml-64 p-8">
        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?php echo escapeHTML($message); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo escapeHTML($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                <?php echo $editMode ? 'Edit Artikel' : 'Tulis Artikel Baru'; ?>
            </h1>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Judul Artikel</label>
                    <input type="text" id="title" name="title" value="<?php echo escapeHTML($title); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div class="mb-4">
                    <label for="category" class="block text-gray-700 font-medium mb-2">Kategori</label>
                    <select id="category" name="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo escapeHTML($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="editor" class="block text-gray-700 font-medium mb-2">Konten Artikel</label>
                    <div id="editor" style="height: 300px;"><?php echo $content; ?></div>
                    <input type="hidden" name="content" id="content-input">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Status Publikasi</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="draft" <?php echo ($status == 'draft') ? 'checked' : ''; ?> class="form-radio text-blue-500">
                            <span class="ml-2">Draft</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="published" <?php echo ($status == 'published') ? 'checked' : ''; ?> class="form-radio text-blue-500">
                            <span class="ml-2">Publikasikan</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="all_article.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200">
                        <?php echo $editMode ? 'Perbarui Artikel' : 'Simpan Artikel'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Quill editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Update hidden form field with Quill's contents before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            document.getElementById('content-input').value = quill.root.innerHTML;
        });
    </script>
</body>

</html>