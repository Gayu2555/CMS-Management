<?php
require 'backend/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urbansiana CMS</title>
    <link href="src/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
</head>


<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-xl">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent">Urbansiana - CMS</h2>
            <p class="text-sm text-gray-400 mt-1">Sistem Manajemen Konten</p>
        </div>
        <nav class="mt-6 space-y-2 px-3">
            <a href="cms.php" class="flex items-center px-3 py-3 rounded-lg text-white bg-gray-800 border-l-4 border-blue-500 group">
                <i class="fas fa-pen-to-square mr-3 text-blue-400 group-hover:text-blue-400"></i>
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
            <a href="list_reporter.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fa-solid fa-binoculars"></i>
                From Reporter Lapangan
            </a>
        </nav>
    </div>


    <!-- Main Content -->
    <div class="ml-64 p-8">
        <div class="max-w-5xl mx-auto">
            <form id="articleForm" method="POST" action="backend/process.php" enctype="multipart/form-data" class="space-y-6">
                <!-- Article Header -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Article Title</label>
                            <input type="text" name="title" id="title" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white" placeholder="Enter article title...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                            <input type="text" name="author" id="author" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" id="category" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">Select Category</option>
                                <!-- PHP will populate categories here -->
                            </select>
                            <script>
                                $(document).ready(function() {
                                    function loadCategories() {
                                        $.ajax({
                                            url: "backend/get_categories.php",
                                            type: "GET",
                                            dataType: "json",
                                            success: function(response) {
                                                if (response.status === "success") {
                                                    let categorySelect = $("#category");
                                                    categorySelect.empty(); // Kosongkan sebelum menambahkan opsi baru
                                                    categorySelect.append(`<option value="">Select Category</option>`);

                                                    response.data.forEach(function(category) {
                                                        categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                                                    });
                                                } else {
                                                    console.error("Gagal memuat kategori:", response.message);
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.error("Error AJAX:", xhr.responseText);
                                            }
                                        });
                                    }

                                    // Panggil fungsi untuk memuat kategori saat halaman dimuat
                                    loadCategories();
                                });
                            </script>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Publication Date</label>
                            <input type="date" name="date_created" id="date_created" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Article Position</label>
                            <select name="position" id="position" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">Select Position</option>
                                <option value="news_list">News List</option>
                                <option value="sub_headline">Sub Headline</option>
                                <option value="headline">Headline</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Editor Section -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <!-- Toolbar -->
                    <div class="flex flex-wrap items-center gap-2 p-3 bg-gray-50 border rounded-lg mb-4">
                        <div class="flex items-center gap-1">
                            <button type="button" id="bold-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-bold"></i>
                            </button>
                            <button type="button" id="italic-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-italic"></i>
                            </button>
                            <button type="button" id="underline-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-underline"></i>
                            </button>
                        </div>

                        <div class="w-px h-6 bg-gray-300"></div>

                        <div class="flex items-center gap-1">
                            <button type="button" id="bullet-list-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-list-ul"></i>
                            </button>
                            <button type="button" id="ordered-list-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-list-ol"></i>
                            </button>
                        </div>

                        <div class="w-px h-6 bg-gray-300"></div>

                        <div class="flex items-center gap-1">
                            <button type="button" id="image-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-image"></i>
                            </button>
                            <button type="button" id="link-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-link"></i>
                            </button>
                            <button type="button" id="read-mode-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-book-reader"></i>
                            </button>
                        </div>

                        <div class="w-px h-6 bg-gray-300"></div>

                        <div class="flex items-center gap-2">
                            <button type="button" id="text-color-button" class="p-2 hover:bg-gray-200 rounded-md transition-colors">
                                <i class="fas fa-palette"></i>
                            </button>
                            <select id="font-size-select" class="px-3 py-2 border rounded-md bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="normal">Normal</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                            </select>
                        </div>

                        <div class="ml-auto flex items-center gap-2">
                            <button type="button" id="read-also-button" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors text-sm font-medium">
                                <i class="fas fa-book mr-2"></i>Read Also
                            </button>
                            <button type="button" id="quote-from-button" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors text-sm font-medium">
                                <i class="fas fa-quote-right mr-2"></i>Quote
                            </button>
                        </div>
                    </div>

                    <!-- Editor -->
                    <div id="editor" class="h-[600px] border rounded-lg mb-6"></div>
                    <input type="hidden" name="content" id="hiddenContent">
                    <!-- Image Upload -->
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors group">
                            <input type="file" id="image" name="image" accept="image/*" class="hidden" required>
                            <label for="image" class="cursor-pointer block">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 group-hover:text-blue-500 transition-colors mb-3"></i>
                                    <p class="text-gray-700 font-medium">Klik untuk mengunggah gambar utama</p>
                                    <p class="text-sm text-gray-500 mt-1">Ukuran file maksimal: 2MB</p>
                                </div>
                            </label>
                            <div id="imagePreview" class="mt-6 hidden">
                                <img src="" alt="Preview" class="max-w-xs mx-auto rounded-lg shadow-md">
                            </div>
                        </div>
                        <input type="text" name="figcaption" id="figcaption" placeholder="Keterangan gambar"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3">
                        <button type="button" id="preview-button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Preview
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Publish Article
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');

            imageInput.addEventListener('change', function() {
                const file = this.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        // Hapus preview sebelumnya jika ada
                        imagePreview.innerHTML = '';

                        // Buat elemen gambar baru
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Preview';
                        img.className = 'max-w-xs mx-auto rounded-lg shadow-md';

                        // Tampilkan preview
                        imagePreview.appendChild(img);
                        imagePreview.classList.remove('hidden');
                    };

                    reader.readAsDataURL(file);
                } else {
                    // Jika tidak ada file yang dipilih, sembunyikan preview
                    imagePreview.classList.add('hidden');
                    imagePreview.innerHTML = '';
                }
            });
        });
    </script>

    <!-- Modals -->
    <!-- Read Mode Modal -->
    <div id="readModeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-4">Read Mode</h2>
            <div id="readModeContent" class="prose max-w-none"></div>
            <button id="closeReadMode" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Close</button>
        </div>
    </div>

    <!-- Modal Baca Juga -->
    <div id="readAlsoModal" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="fixed inset-0 bg-black bg-opacity-60"></div>
        <div class="relative bg-white p-6 rounded-xl shadow-lg w-96 max-w-[90%]">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Tambahkan "Baca Juga"</h2>
            <input type="text" id="readAlsoTitle" placeholder="Judul Artikel" class="w-full p-2.5 border border-gray-300 rounded-lg mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="url" id="readAlsoUrl" placeholder="URL Artikel" class="w-full p-2.5 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex justify-end gap-2">
                <button id="cancelReadAlso" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button id="insertReadAlso" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tambah</button>
            </div>
        </div>
    </div>

    <!-- Modal Kutipan -->
    <div id="quoteFromModal" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="fixed inset-0 bg-black bg-opacity-60"></div>
        <div class="relative bg-white p-6 rounded-xl shadow-lg w-96 max-w-[90%]">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Tambahkan Kutipan</h2>
            <textarea id="quoteText" placeholder="Teks Kutipan" class="w-full p-2.5 border border-gray-300 rounded-lg mb-3 h-24 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            <input type="text" id="quoteSource" placeholder="Sumber Kutipan" class="w-full p-2.5 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex justify-end gap-2">
                <button id="cancelQuoteFrom" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button id="insertQuoteFrom" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tambah</button>
            </div>
        </div>
    </div>
    <script>
        // Load categories on page load
        // Handle form submission
        // Handle form submission
        $('#articleForm').on('submit', function(e) {
            e.preventDefault();

            // Pastikan konten editor disimpan ke hidden field
            $('#hiddenContent').val(quill.root.innerHTML);

            // Validasi form
            const requiredFields = ["title", "author", "category", "date_created", "position"];
            const isEmpty = requiredFields.some(id => !document.getElementById(id)?.value);

            if (isEmpty) {
                alert("Harap isi semua kolom wajib");
                return false;
            }

            // Buat FormData dan kirim ke server
            var formData = new FormData(this);
            formData.append('action', 'saveArticle');

            $.ajax({
                url: 'backend/process.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Respons:', response); // Cek respons aktual
                    if (response && response.success) {
                        alert('Artikel berhasil disimpan!');
                        window.location.href = 'all_article.php';
                    } else {
                        alert(response.message || 'Error menyimpan artikel');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', status, error);
                    console.log('Teks Respons:', xhr.responseText);
                    alert('Error saat menyimpan artikel: ' + error);
                }
            });
        });
    </script>

    <script src="js/cms.js"></script>
</body>

</html>