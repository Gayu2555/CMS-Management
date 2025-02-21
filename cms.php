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
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-xl z-20">
        <div class="p-6">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-500 to-teal-400 bg-clip-text text-transparent">Urbansiana</h2>
            <p class="text-sm text-gray-400 mt-1">Content Management System</p>
        </div>

        <nav class="mt-6 px-4 space-y-1">
            <a href="#" class="flex items-center px-4 py-3 rounded-lg bg-blue-600 text-white group">
                <i class="fas fa-pen-to-square mr-3"></i>
                <span class="font-medium">Write Article</span>
            </a>

            <a href="#" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-newspaper mr-3 opacity-75"></i>
                <span>All Articles</span>
            </a>

            <a href="#" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-folder mr-3 opacity-75"></i>
                <span>Categories</span>
            </a>

            <a href="#" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-all duration-200 group">
                <i class="fas fa-binoculars mr-3 opacity-75"></i>
                <span>Field Reports</span>
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
                    <div id="editor" class="h-96 border rounded-lg mb-6"></div>
                    <input type="hidden" name="content" id="hiddenContent">

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
    <div id="readAlsoModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg max-w-md w-full">
            <h2 class="text-3xl font-semibold mb-6 text-gray-800">Tambahkan "Baca Juga"</h2>
            <input type="text" id="readAlsoTitle" placeholder="Judul Artikel" class="w-full p-3 border border-gray-300 rounded-lg mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="url" id="readAlsoUrl" placeholder="URL Artikel" class="w-full p-3 border border-gray-300 rounded-lg mb-5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex justify-end gap-3">
                <button id="cancelReadAlso" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button id="insertReadAlso" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tambah</button>
            </div>
        </div>
    </div>

    <!-- Modal Kutipan -->
    <div id="quoteFromModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg max-w-md w-full">
            <h2 class="text-3xl font-semibold mb-6 text-gray-800">Tambahkan Kutipan</h2>
            <textarea id="quoteText" placeholder="Teks Kutipan" class="w-full p-3 border border-gray-300 rounded-lg mb-3 h-28 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            <input type="text" id="quoteSource" placeholder="Sumber Kutipan" class="w-full p-3 border border-gray-300 rounded-lg mb-5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex justify-end gap-3">
                <button id="cancelQuoteFrom" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button id="insertQuoteFrom" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tambah</button>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Initialize Quill
            const quill = new Quill("#editor", {
                theme: "snow",
                modules: {
                    toolbar: false
                }
            });

            // Initialize all modal elements
            const readModeModal = document.getElementById("readModeModal");
            const readAlsoModal = document.getElementById("readAlsoModal");
            const quoteFromModal = document.getElementById("quoteFromModal");
            const readModeContent = document.getElementById("readModeContent");

            // Basic formatting buttons
            const formatButtons = ["bold", "italic", "underline"];
            formatButtons.forEach(type => {
                document.getElementById(`${type}-button`).onclick = () => {
                    quill.format(type, !quill.getFormat()[type]);
                };
            });

            document.getElementById("bullet-list-button").onclick = () => quill.format('list', 'bullet');
            document.getElementById("ordered-list-button").onclick = () => quill.format('list', 'ordered');

            // Text color button
            document.getElementById("text-color-button").onclick = () => {
                const color = prompt("Masukkan kode warna (contoh: #FF0000):");
                if (color) quill.format('color', color);
            };

            // Font size select
            document.getElementById("font-size-select").onchange = (e) => {
                const value = e.target.value;
                quill.format('header', value === 'normal' ? false : parseInt(value.replace('h', '')));
            };

            // Image insertion
            document.getElementById("image-button").onclick = () => {
                const url = prompt("Masukkan URL gambar:");
                if (url) {
                    const selection = quill.getSelection();
                    const index = selection ? selection.index : quill.getLength();
                    quill.insertEmbed(index, 'image', url);
                }
            };

            // Link insertion
            document.getElementById("link-button").onclick = () => {
                const url = prompt("Masukkan URL:");
                if (url) {
                    const range = quill.getSelection();
                    if (range) {
                        if (range.length > 0) {
                            quill.format('link', url);
                        } else {
                            const text = prompt("Masukkan teks tautan:");
                            if (text) {
                                quill.insertText(range.index, text, {
                                    link: url
                                });
                            }
                        }
                    }
                }
            };

            // Read Mode functionality
            document.getElementById("read-mode-button").onclick = () => {
                readModeContent.innerHTML = quill.root.innerHTML;
                readModeModal.classList.replace("hidden", "flex");
            };

            document.getElementById("closeReadMode").onclick = () => {
                readModeModal.classList.replace("flex", "hidden");
            };

            // Read Also functionality
            document.getElementById("read-also-button").onclick = () => {
                readAlsoModal.classList.replace("hidden", "flex");
            };

            document.getElementById("cancelReadAlso").onclick = () => {
                readAlsoModal.classList.replace("flex", "hidden");
                ["readAlsoTitle", "readAlsoUrl"].forEach(id => document.getElementById(id).value = "");
            };

            document.getElementById("insertReadAlso").onclick = () => {
                const title = document.getElementById("readAlsoTitle").value;
                const url = document.getElementById("readAlsoUrl").value;
                if (title && url) {
                    const html = `
                <div class="read-also p-4 bg-gray-50 rounded-lg my-4">
                    <p><strong>Baca Juga:</strong> <a href="${url}" target="_blank" class="text-blue-600 hover:underline">${title}</a></p>
                </div>`;
                    const range = quill.getSelection(true);
                    quill.clipboard.dangerouslyPasteHTML(range.index, html);
                    readAlsoModal.classList.replace("flex", "hidden");
                    ["readAlsoTitle", "readAlsoUrl"].forEach(id => document.getElementById(id).value = "");
                }
            };

            // Quote functionality
            document.getElementById("quote-from-button").onclick = () => {
                quoteFromModal.classList.replace("hidden", "flex");
            };

            document.getElementById("cancelQuoteFrom").onclick = () => {
                quoteFromModal.classList.replace("flex", "hidden");
                ["quoteText", "quoteSource"].forEach(id => document.getElementById(id).value = "");
            };

            document.getElementById("insertQuoteFrom").onclick = () => {
                const text = document.getElementById("quoteText").value;
                const source = document.getElementById("quoteSource").value;
                if (text && source) {
                    const html = `
                <div class="quote-from p-4 bg-gray-50 border-l-4 border-blue-500 rounded-lg my-4">
                    <blockquote class="text-gray-700 italic">"${text}"</blockquote>
                    <p class="mt-2 text-sm text-gray-600">Sumber: <span class="font-medium">${source}</span></p>
                </div>`;
                    const range = quill.getSelection(true);
                    quill.clipboard.dangerouslyPasteHTML(range.index, html);
                    quoteFromModal.classList.replace("flex", "hidden");
                    ["quoteText", "quoteSource"].forEach(id => document.getElementById(id).value = "");
                }
            };

            // Handle click on quote sources
            quill.root.addEventListener("click", (event) => {
                const sourceElement = event.target.closest("[data-source]");
                if (sourceElement) {
                    event.preventDefault();
                    alert("Sumber kutipan: " + sourceElement.getAttribute("data-source"));
                }
            });

            // Form submission
            document.getElementById("articleForm").onsubmit = (e) => {
                document.getElementById("hiddenContent").value = quill.root.innerHTML;
                const fields = ["title", "author", "category", "date_created", "position"];
                const empty = fields.some(id => !document.getElementById(id).value);
                if (empty) {
                    alert("Harap isi semua kolom wajib");
                    e.preventDefault();
                    return false;
                }
                return true;
            };

            // Preview functionality
            document.getElementById("preview-button").onclick = () => {
                readModeContent.innerHTML = quill.root.innerHTML;
                readModeModal.classList.replace("hidden", "flex");
            };

            // Close modals when clicking outside
            [readModeModal, readAlsoModal, quoteFromModal].forEach(modal => {
                modal.addEventListener("click", (e) => {
                    if (e.target === modal) {
                        modal.classList.replace("flex", "hidden");
                    }
                });
            });

            // Handle keyboard shortcuts
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    [readModeModal, readAlsoModal, quoteFromModal].forEach(modal => {
                        modal.classList.replace("flex", "hidden");
                    });
                }
            });
        });
    </script>
</body>

</html>