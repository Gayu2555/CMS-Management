<?php
// Include configuration file
require_once 'backend/config.php';

// Initialize database connection
try {
    $pdo = connectDB();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle article deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$_GET['delete']]);

        // Redirect to avoid resubmission on refresh
        header("Location: all_article.php?deleted=true");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal menghapus artikel: " . $e->getMessage();
    }
}

// Set up pagination
$articlesPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $articlesPerPage;

// Set up search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = '';
$params = [];

if (!empty($search)) {
    $searchCondition = "WHERE a.title LIKE ?";
    $params[] = "%$search%";
}

// Count total articles for pagination
try {
    $countQuery = "SELECT COUNT(*) FROM articles a $searchCondition";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalArticles = $countStmt->fetchColumn();
    $totalPages = ceil($totalArticles / $articlesPerPage);
} catch (PDOException $e) {
    $error = "Gagal menghitung artikel: " . $e->getMessage();
    $totalArticles = 0;
    $totalPages = 0;
}

// Fetch articles
try {
    $query = "SELECT a.id, a.title, c.name AS category, a.date_published, a.author_name, a.image_url
              FROM articles a
              LEFT JOIN categories c ON a.category_id = c.id
              $searchCondition
              ORDER BY a.date_published DESC
              LIMIT $offset, $articlesPerPage";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil artikel: " . $e->getMessage();
    $articles = [];
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
    <title>Semua Artikel - Urbansiana CMS</title>
</head>

<body class="bg-gray-100">
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-xl">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent">Urbansiana - CMS</h2>
            <p class="text-sm text-gray-400 mt-1">Sistem Manajemen Konten</p>
        </div>
        <nav class="mt-6 space-y-2 px-3">
            <a href="cms.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-pen-to-square mr-3 text-gray-400 group-hover:text-blue-400"></i>
                Tulis artikel
            </a>
            <a href="all_article.php" class="flex items-center px-3 py-3 rounded-lg text-white bg-gray-800 border-l-4 border-blue-500 group">
                <i class="fas fa-newspaper mr-3 text-blue-400 group-hover:text-blue-400"></i>
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
        <!-- Notification messages -->
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>Artikel berhasil dihapus.</p>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo escapeHTML($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Artikel yang Dipublikasi</h1>
                <div class="flex space-x-4">
                    <form action="" method="GET" class="flex">
                        <div class="relative">
                            <input type="text" name="search" value="<?php echo escapeHTML($search); ?>" placeholder="Cari artikel..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <button type="submit" class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                            Cari
                        </button>
                    </form>
                    <a href="cms.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Artikel Baru
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Publikasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($articles)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada artikel yang ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($article['image_url'])): ?>
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="<?php echo escapeHTML($article['image_url']); ?>" alt="<?php echo escapeHTML($article['title']); ?>">
                                                </div>
                                            <?php endif; ?>
                                            <div class="<?php echo !empty($article['image_url']) ? 'ml-4' : ''; ?>">
                                                <div class="text-sm font-medium text-gray-900"><?php echo escapeHTML($article['title']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo escapeHTML($article['category'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d M Y', strtotime($article['date_published'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo escapeHTML($article['author_name'] ?? 'Anonymous'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="cms.php?edit=<?php echo $article['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $article['id']; ?>)" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $articlesPerPage, $totalArticles); ?> dari <?php echo $totalArticles; ?> artikel
                </div>
                <?php if ($totalPages > 1): ?>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 border rounded-md hover:bg-gray-50">Previous</a>
                        <?php else: ?>
                            <button disabled class="px-3 py-1 border rounded-md opacity-50 cursor-not-allowed">Previous</button>
                        <?php endif; ?>

                        <?php
                        // Show a limited number of page links
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <?php if ($i == $page): ?>
                                <button class="px-3 py-1 bg-blue-500 text-white rounded-md"><?php echo $i; ?></button>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 border rounded-md hover:bg-gray-50"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-1 border rounded-md hover:bg-gray-50">Next</a>
                        <?php else: ?>
                            <button disabled class="px-3 py-1 border rounded-md opacity-50 cursor-not-allowed">Next</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal Script -->
    <script>
        function confirmDelete(articleId) {
            if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
                window.location.href = 'all_article.php?delete=' + articleId;
            }
        }
    </script>
</body>

</html>