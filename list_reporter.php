<?php
// field_reporter.php - Main page for listing field reports
require_once 'backend/config.php';

// Initialize report fetching
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// This will be used by AJAX/jQuery
if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    include 'backend/get_reports.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CikarangTimes - Reporter Lapangan</title>
    <link href="src/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-xl">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent">Urbansiana - CMS</h2>
            <p class="text-sm text-gray-400 mt-1">Sistem Manajemen Konten</p>
        </div>
        <nav class="mt-6 space-y-2 px-3">
            <a href="index.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-pen-to-square mr-3 text-gray-400 group-hover:text-blue-400"></i>
                Tulis Berita
            </a>
            <a href="all_article.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-newspaper mr-3 text-gray-400 group-hover:text-blue-400"></i>
                Semua Artikel
            </a>
            <a href="category.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-folder mr-3 text-gray-400"></i>
                Kategori
            </a>
            <a href="list_reporter.php" class="flex items-center px-3 py-3 rounded-lg text-white bg-gray-800 border-l-4 border-blue-500 group">
                <i class="fa-solid fa-binoculars mr-3 text-blue-400"></i>
                From Reporter Lapangan
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Reporter Lapangan</h1>
            <div class="flex space-x-3">
                <div class="relative">
                    <select id="statusFilter" class="appearance-none bg-white border border-gray-300 rounded-lg py-2 px-4 pr-8 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="reviewed" <?php echo $statusFilter == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                        <option value="approved" <?php echo $statusFilter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $statusFilter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </div>
                </div>
                <span id="reportsCount" class="inline-block bg-blue-100 text-blue-800 text-xs px-2 rounded-full py-1 font-semibold whitespace-nowrap">
                    Loading...
                </span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div id="statsCards" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Stats will be loaded by AJAX -->
            <div class="animate-pulse bg-gray-200 h-24 rounded-lg"></div>
            <div class="animate-pulse bg-gray-200 h-24 rounded-lg"></div>
            <div class="animate-pulse bg-gray-200 h-24 rounded-lg"></div>
            <div class="animate-pulse bg-gray-200 h-24 rounded-lg"></div>
        </div>

        <!-- Reports Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="reportsTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Reports will be loaded by AJAX -->
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading data...</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div id="pagination" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <!-- Pagination will be loaded by AJAX -->
                <div class="flex justify-center w-full">
                    <div class="animate-pulse bg-gray-200 h-8 w-64 rounded"></div>
                </div>
            </div>
            <div id="notificationContainer" class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-md"></div>
        </div>
    </div>

    <script src="js/field_reporter.js"></script>
</body>

</html>