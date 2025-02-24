<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d388dc6636.js" crossorigin="anonymous"></script>
    <title>CikarangTimes - Manajemen Kategori</title>
</head>

<body class="bg-gray-50">
    <div class="flex">
        <!-- Sidebar -->
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
                <a href="all_article.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                    <i class="fas fa-newspaper mr-3 group-hover:text-blue-400"></i>
                    Semua Artikel
                </a>
                <a href="category.php" class="flex items-center px-3 py-3 rounded-lg text-white bg-gray-800 border-l-4 border-blue-500 group">
                    <i class="fas fa-folder mr-3 group-hover:text-blue-400"></i>
                    Kategori
                </a>
                <a href="list_reporter.php" class="flex items-center px-3 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                    <i class="fa-solid fa-binoculars  text-blue-400 group-hover:text-blue-800"></i>
                    From Reporter Lapangan
                </a>
            </nav>
        </div>

        <!-- Toast Notification -->
        <div id="toast-container" class="fixed bottom-5 right-5 space-y-3 z-50"></div>

        <!-- Main Content -->
        <div class="ml-64 flex-1 p-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Kategori</h1>
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        <span id="current-time"></span>
                    </span>
                </div>

                <!-- Category Form -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Tambah Kategori Baru</h2>
                    <form id="category-form" class="space-y-4">
                        <div>
                            <label for="category-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                            <input type="text" id="category-name" name="category-name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan nama kategori">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="submit-btn" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>

    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            console.log("jQuery siap!"); // Debugging

            $("#category-form").submit(function(e) {
                e.preventDefault();
                console.log("Form submit ter-trigger!"); // Debugging

                let categoryName = $("#category-name").val().trim();

                if (categoryName === "") {
                    showToast("Nama kategori tidak boleh kosong!", "error");
                    return;
                }

                $.ajax({
                    url: "backend/save_category.php",
                    type: "POST",
                    data: {
                        category_name: categoryName
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log("Respon dari server:", response); // Debugging
                        if (response.status === "success") {
                            $("#category-name").val(""); // Kosongkan input setelah berhasil
                        }
                        showToast(response.message, response.status);
                    },
                    error: function(xhr, status, error) {
                        console.log("Error AJAX:", xhr.responseText); // Debugging
                        showToast("Terjadi kesalahan saat mengirim data.", "error");
                    }
                });
            });

            function showToast(message, type) {
                console.log("Menampilkan toast:", message, type); // Debugging

                let bgColor = type === "success" ? "bg-green-500" : "bg-red-500";
                let toast = $(`
            <div class="flex items-center ${bgColor} text-white text-sm font-medium px-4 py-3 rounded-lg shadow-md fixed top-5 right-5 opacity-0 transition-all duration-300">
                <span class="mr-2">${message}</span>
                <button onclick="$(this).parent().remove()" class="ml-auto focus:outline-none">✖</button>
            </div>
        `);

                $("#toast-container").append(toast);
                setTimeout(() => toast.css("opacity", "1"), 100); // Fade in
                setTimeout(() => toast.fadeOut(500, () => toast.remove()), 3000); // Auto remove
            }
        });
    </script>

</body>

</html>