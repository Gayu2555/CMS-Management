<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CikarangTimes Dashboard</title>
    <link href="src/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://kit.fontawesome.com/d388dc6636.js" crossorigin="anonymous"></script>
</head>
<?php
require 'backend/get_category.php';
?>

<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-xl">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent">Urbansiana - CMS</h2>
            <p class="text-sm text-gray-400 mt-1">Sistem Manajemen Konten</p>
        </div>
        <nav class="mt-6 space-y-2 px-3">
            <a href="#" class="flex items-center px-3 py-3 rounded-lg text-white bg-gray-800 border-l-4 border-blue-500 group">
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
            <a href="#" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fa-solid fa-binoculars"></i>
                From Reporter Lapangan
            </a>
        </nav>
    </div>


    <!-- Main Content -->
    <div class="ml-16 lg:ml-64 p-4 lg:p-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 lg:mb-8">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Tulis Artikel Baru</h1>
                <p class="text-gray-600 mt-1">Buat dan publikasikan artikel berita Anda</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-700">Selamat datang, Admin</span>
                <img src="https://via.placeholder.com/32" alt="Admin" class="w-8 h-8 rounded-full ring-2 ring-gray-200">
            </div>
        </div>

        <!-- Main Form Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <!-- Alert Messages -->
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo $error; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700"><?php echo $success; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Article Form -->
            <form method="POST" action="backend/process.php" enctype="multipart/form-data" id="articleForm" class="space-y-6">
                <!-- Article Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Artikel</label>
                        <input type="text" name="title" id="title" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                    <div class="space-y-2">
                        <label for="author" class="block text-sm font-medium text-gray-700">Penulis</label>
                        <input type="text" name="author" id="author" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="date_created" class="block text-sm font-medium text-gray-700">Tanggal Publikasi</label>
                        <input type="date" name="date_created" id="date_created" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                    <div class="space-y-2">
                        <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category" id="category" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['TABLE_NAME']); ?>">
                                    <?php echo ucfirst($category['TABLE_NAME']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Position Selection -->
                <div class="space-y-2">
                    <label for="position" class="block text-sm font-medium text-gray-700">Posisi Artikel</label>
                    <select name="position" id="position" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                        <option value="">Pilih Posisi</option>
                        <option value="news_list">News List</option>
                        <option value="sub_headline">Sub Headline</option>
                        <option value="headline">Headline</option>
                    </select>
                </div>

                <!-- Toolbar with Tooltips -->
                <div class="flex flex-wrap items-center gap-2 p-3 bg-gray-50 border rounded-lg">
                    <div class="flex items-center gap-1">
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Tebal">
                            <i class="fas fa-bold"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Tebal</span>
                        </button>
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Miring">
                            <i class="fas fa-italic"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Miring</span>
                        </button>
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Garis Bawah">
                            <i class="fas fa-underline"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Garis Bawah</span>
                        </button>
                    </div>

                    <div class="w-px h-6 bg-gray-300"></div>

                    <div class="flex items-center gap-1">
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Daftar Poin">
                            <i class="fas fa-list-ul"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Daftar Poin</span>
                        </button>
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Daftar Nomor">
                            <i class="fas fa-list-ol"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Daftar Nomor</span>
                        </button>
                    </div>

                    <div class="w-px h-6 bg-gray-300"></div>

                    <div class="flex items-center gap-1">
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Sisipkan Gambar">
                            <i class="fas fa-image"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Sisipkan Gambar</span>
                        </button>
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Sisipkan Tautan">
                            <i class="fas fa-link"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Sisipkan Tautan</span>
                        </button>
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Mode Baca">
                            <i class="fas fa-book-reader"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Mode Baca</span>
                        </button>
                    </div>

                    <div class="w-px h-6 bg-gray-300"></div>

                    <div class="flex items-center gap-2">
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Warna Teks">
                            <i class="fas fa-palette"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Warna Teks</span>
                        </button>
                        <select id="font-size-select"
                            class="px-3 py-2 border rounded-md bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="normal">Normal</option>
                            <option value="h1">Judul 1</option>
                            <option value="h2">Judul 2</option>
                            <option value="h3">Judul 3</option>
                        </select>
                    </div>

                    <div class="w-px h-6 bg-gray-300"></div>

                    <div class="flex items-center gap-1">
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Baca Juga">
                            <i class="fas fa-book"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Baca Juga</span>
                        </button>
                        <button type="button"
                            class="p-2 hover:bg-gray-200 rounded-md transition-colors group relative"
                            aria-label="Dikutip Dari">
                            <i class="fas fa-quote-right"></i>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Dikutip Dari</span>
                        </button>
                    </div>
                </div>

                <!-- Editor Area -->
                <div id="editor" class="min-h-[24rem] border rounded-lg p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
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
                <div class="flex justify-end gap-4">
                    <button type="button" id="saveDraftButton"
                        class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-4 focus:ring-gray-200 transition duration-150 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Simpan Draf</span>
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition duration-150 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Publikasikan Artikel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="readModeModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white w-11/12 lg:w-3/4 h-3/4 rounded-xl shadow-xl">
            <div class="h-full flex flex-col">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-6 border-b">
                    <h2 class="text-2xl font-bold text-gray-900">Mode Pratinjau</h2>
                    <button id="closeReadMode" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="max-w-4xl mx-auto">
                        <!-- Article Header -->
                        <div class="mb-8">
                            <h1 id="previewTitle" class="text-3xl font-bold text-gray-900 mb-3"></h1>
                            <div class="flex items-center gap-2 text-gray-600 text-sm">
                                <span>tim</span>
                                <span class="mx-2">|</span>
                                <span id="previewAuthor" class="font-medium"></span>
                                <time id="previewDate" class="ml-4"></time>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div id="previewImageContainer" class="mb-6 hidden">
                            <img id="previewImage" src="" alt="" class="w-full h-auto rounded-lg mb-2">
                            <p id="previewCaption" class="text-sm text-gray-600 italic"></p>
                        </div>

                        <!-- Article Content -->
                        <div id="previewContent" class="prose max-w-none">
                            <!-- Content will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('readModeModal');
            const closeBtn = document.getElementById('closeReadMode');
            const readModeBtn = document.getElementById('read-mode-button');

            // Format date function
            function formatDate(dateString) {
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZone: 'Asia/Jakarta'
                };
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', options) + ' WIB';
            }

            // Show preview
            function showPreview() {
                // Get form values
                const title = document.getElementById('title').value;
                const author = document.getElementById('author').value;
                const date = document.getElementById('date_created').value; // If date_created exists in the form
                const content = document.getElementById('editor').innerHTML; // Editor content
                const imageFile = document.getElementById('image').files[0];
                const caption = document.getElementById('figcaption').value;

                // Update preview content
                document.getElementById('previewTitle').textContent = title;
                document.getElementById('previewAuthor').textContent = author;
                document.getElementById('previewDate').textContent = formatDate(date);
                document.getElementById('previewContent').innerHTML = content;

                // Handle image preview
                const imageContainer = document.getElementById('previewImageContainer');
                const previewImage = document.getElementById('previewImage');
                const previewCaption = document.getElementById('previewCaption');

                if (imageFile) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imageContainer.classList.remove('hidden');
                        if (caption) {
                            previewCaption.textContent = caption;
                        }
                    };
                    reader.readAsDataURL(imageFile);
                } else {
                    imageContainer.classList.add('hidden'); // Hide the image container if no image is selected
                }

                // Show modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            // Close preview
            function closePreview() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            // Event listeners
            readModeBtn.addEventListener('click', showPreview);
            closeBtn.addEventListener('click', closePreview);

            // Close on outside click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closePreview();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closePreview();
                }
            });
        });
    </script>

    <!-- Read Also Modal -->
    <div id="readAlsoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white w-1/2 rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-4">Tambah Baca Juga</h2>
            <input type="text" id="readAlsoTitle" placeholder="Judul artikel" class="w-full px-4 py-2 border rounded-lg mb-4">
            <input type="text" id="readAlsoUrl" placeholder="URL artikel" class="w-full px-4 py-2 border rounded-lg mb-4">
            <div class="flex justify-end">
                <button id="cancelReadAlso" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg mr-2">Batal</button>
                <button id="insertReadAlso" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Tambahkan</button>
            </div>
        </div>
    </div>

    <!-- Quote From Modal -->
    <div id="quoteFromModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white w-1/2 rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-4">Tambah Kutipan</h2>
            <textarea id="quoteText" placeholder="Teks kutipan" class="w-full px-4 py-2 border rounded-lg mb-4" rows="4"></textarea>
            <input type="text" id="quoteSource" placeholder="Sumber kutipan" class="w-full px-4 py-2 border rounded-lg mb-4">
            <div class="flex justify-end">
                <button id="cancelQuoteFrom" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg mr-2">Batal</button>
                <button id="insertQuoteFrom" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Tambahkan</button>
            </div>
        </div>
    </div>

    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: false
            }
        });

        // Add this at the top of your JavaScript code
        const alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        document.querySelector('#articleForm').insertAdjacentElement('beforebegin', alertContainer);

        // Replace the form submission code
        document.getElementById('articleForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = 'Memproses...';
            submitButton.disabled = true;

            // Create FormData object
            const formData = new FormData(this);

            // Properly handle Quill content
            const quillContent = quill.getContents();
            formData.set('content', JSON.stringify(quillContent));

            // Send AJAX request
            fetch('backend/process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(text => {
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Error parsing JSON:', text);
                        throw new Error('Invalid JSON response from server');
                    }

                    if (data.success) {
                        showAlert('success', data.message);
                        // Optional: Reset form
                        this.reset();
                        quill.setContents([]);
                        document.getElementById('imagePreview').classList.add('hidden');
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat mengirim artikel. Silakan coba lagi.');
                })
                .finally(() => {
                    // Restore button state
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
        });

        // Add helper function for showing alerts
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            if (!alertContainer) return;

            const alertClass = type === 'success' ?
                'bg-green-100 border-green-400 text-green-700' :
                'bg-red-100 border-red-400 text-red-700';

            alertContainer.innerHTML = `
        <div class="${alertClass} px-4 py-3 rounded relative border mb-4" role="alert">
            <strong class="font-bold">${type === 'success' ? 'Sukses!' : 'Error!'}</strong>
            <span class="block sm:inline"> ${message}</span>
        </div>
    `;

            // Auto-hide alert after 5 seconds
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        // Handle image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.classList.remove('hidden');
                    preview.querySelector('img').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Replace DOMNodeInserted listener with MutationObserver
        const targetNode = document.body;
        const config = {
            childList: true,
            subtree: true
        };
        const callback = function(mutationsList, observer) {
            for (const mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    // Handle your scroll logic here
                }
            }
        };
        const observer = new MutationObserver(callback);
        observer.observe(targetNode, config);
        // Custom toolbar functionality
        document.getElementById('bold-button').addEventListener('click', () => {
            quill.format('bold', !quill.getFormat().bold);
        });

        document.getElementById('italic-button').addEventListener('click', () => {
            quill.format('italic', !quill.getFormat().italic);
        });

        document.getElementById('underline-button').addEventListener('click', () => {
            quill.format('underline', !quill.getFormat().underline);
        });

        document.getElementById('bullet-list-button').addEventListener('click', () => {
            quill.format('list', 'bullet');
        });

        document.getElementById('ordered-list-button').addEventListener('click', () => {
            quill.format('list', 'ordered');
        });

        document.getElementById('image-button').addEventListener('click', () => {
            const url = prompt('Masukkan URL gambar:');
            if (url) {
                quill.insertEmbed(quill.getSelection().index, 'image', url);
            }
        });

        document.getElementById('link-button').addEventListener('click', () => {
            const url = prompt('Masukkan URL:');
            if (url) {
                const range = quill.getSelection();
                if (range) {
                    quill.format('link', url);
                }
            }
        });

        document.getElementById('text-color-button').addEventListener('click', () => {
            const color = prompt('Masukkan kode warna (contoh: #FF0000):');
            if (color) {
                quill.format('color', color);
            }
        });

        document.getElementById('font-size-select').addEventListener('change', (e) => {
            if (e.target.value === 'normal') {
                quill.format('header', false);
            } else {
                quill.format('header', e.target.value.replace('h', ''));
            }
        });

        // Read Mode functionality
        const readModeModal = document.getElementById('readModeModal');
        const readModeContent = document.getElementById('readModeContent');
        const closeReadMode = document.getElementById('closeReadMode');

        document.getElementById('read-mode-button').addEventListener('click', () => {
            readModeContent.innerHTML = quill.root.innerHTML;
            readModeModal.classList.remove('hidden');
            readModeModal.classList.add('flex');
        });

        closeReadMode.addEventListener('click', () => {
            readModeModal.classList.add('hidden');
            readModeModal.classList.remove('flex');
        });

        // Image preview functionality
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.querySelector('img').src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        const articleForm = document.getElementById('articleForm');
        const hiddenContent = document.getElementById('hiddenContent');
        // Save Draft functionality
        document.getElementById('saveDraftButton').addEventListener('click', () => {
            // Implement draft saving logic here
            alert('Draf disimpan!');
        });

        // Fungsi Baca Juga
        const readAlsoModal = document.getElementById('readAlsoModal');
        const readAlsoTitle = document.getElementById('readAlsoTitle');
        const readAlsoUrl = document.getElementById('readAlsoUrl');
        const cancelReadAlso = document.getElementById('cancelReadAlso');
        const insertReadAlso = document.getElementById('insertReadAlso');

        document.getElementById('read-also-button').addEventListener('click', () => {
            readAlsoModal.classList.remove('hidden');
            readAlsoModal.classList.add('flex');
        });

        cancelReadAlso.addEventListener('click', () => {
            readAlsoModal.classList.add('hidden');
            readAlsoModal.classList.remove('flex');
        });

        insertReadAlso.addEventListener('click', () => {
            const title = readAlsoTitle.value;
            const url = readAlsoUrl.value;
            if (title && url) {
                const readAlsoHtml = `<div class="read-also"><p><strong>Baca Juga:</strong> <a href="${url}" target="_blank">${title}</a></p></div>`;
                const range = quill.getSelection(true);
                quill.insertText(range.index, '\n');
                quill.clipboard.dangerouslyPasteHTML(range.index + 1, readAlsoHtml);
                quill.setSelection(range.index + 2);
                readAlsoModal.classList.add('hidden');
                readAlsoModal.classList.remove('flex');
                readAlsoTitle.value = '';
                readAlsoUrl.value = '';
            }
        });

        // Fungsi Dikutip Dari
        const quoteFromModal = document.getElementById('quoteFromModal');
        const quoteText = document.getElementById('quoteText');
        const quoteSource = document.getElementById('quoteSource');
        const cancelQuoteFrom = document.getElementById('cancelQuoteFrom');
        const insertQuoteFrom = document.getElementById('insertQuoteFrom');

        document.getElementById('quote-from-button').addEventListener('click', () => {
            quoteFromModal.classList.remove('hidden');
            quoteFromModal.classList.add('flex');
        });

        cancelQuoteFrom.addEventListener('click', () => {
            quoteFromModal.classList.add('hidden');
            quoteFromModal.classList.remove('flex');
        });

        insertQuoteFrom.addEventListener('click', () => {
            const text = quoteText.value;
            const source = quoteSource.value;
            if (text && source) {
                const quoteHtml = `<div class="quote-from"><p>Dikutip dari: <a href="#" data-source="${source}">"${text}"</a></p></div>`;
                const range = quill.getSelection(true);
                quill.insertText(range.index, '\n');
                quill.clipboard.dangerouslyPasteHTML(range.index + 1, quoteHtml);
                quill.setSelection(range.index + 2);
                quoteFromModal.classList.add('hidden');
                quoteFromModal.classList.remove('flex');
                quoteText.value = '';
                quoteSource.value = '';
            }
        });

        quill.root.addEventListener('click', function(event) {
            const anchor = event.target.closest('a[data-source]');
            if (anchor) {
                event.preventDefault();
                const source = anchor.getAttribute('data-source');
                alert('Sumber kutipan: ' + source);
            }
        });
    </script>
</body>

</html>