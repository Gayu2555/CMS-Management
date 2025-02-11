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
    <title>Document</title>
</head>

<body class="bg-gray-100">
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-xl">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent">Urbansiana - CMS</h2>
            <p class="text-sm text-gray-400 mt-1">Sistem Manajemen Konten</p>
        </div>
        <nav class="mt-6 space-y-2 px-3">
            <a href="index.php" class="flex items-center px-3 py-3 rounded-lg text-white bg-gray-800 border-l-4 border-blue-500 group">
                <i class="fas fa-pen-to-square mr-3 text-gray-400 group-hover:text-blue-400"></i>
                Tulis artikel
            </a>
            <a href="#" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-newspaper mr-3 text-gray-400 group-hover:text-blue-400"></i>
                Semua Artikel
            </a>
            <a href="category.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-folder mr-3 text-blue-400"></i>
                Kategori
            </a>
            <a href="all_article.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fa-regular fa-list mr-3 text-gray-400 group-hover:text-blue-400"></i>
                Daftar Artikel
            </a>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="ml-64 p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Artikel yang Dipublikasi</h1>
                <div class="flex space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Cari artikel..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Artikel Baru
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Publikasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">10 Tips Menulis Artikel yang Baik</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Tutorial</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-02-11</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dipublikasi</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-edit"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <!-- More rows can be added here -->
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan 1 - 10 dari 50 artikel
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Previous</button>
                    <button class="px-3 py-1 bg-blue-500 text-white rounded-md">1</button>
                    <button class="px-3 py-1 border rounded-md hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border rounded-md hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Next</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>