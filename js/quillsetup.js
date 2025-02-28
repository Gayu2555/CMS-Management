document.addEventListener("DOMContentLoaded", function () {
  // Cek apakah elemen editor ada di halaman
  const editorContainer = document.getElementById("editor");
  if (!editorContainer) {
    console.warn("Elemen editor tidak ditemukan di halaman ini.");
    return;
  }

  // Konfigurasi toolbar Quill
  const toolbarOptions = [
    [{ header: [1, 2, 3, 4, 5, 6, false] }],
    ["bold", "italic", "underline"],
    [{ list: "ordered" }, { list: "bullet" }],
    [{ color: [] }],
    ["link", "image"],
  ];

  // Inisialisasi Quill
  const quill = new Quill("#editor", {
    theme: "snow",
    modules: {
      toolbar: toolbarOptions,
    },
    placeholder: "Tulis konten artikel Anda di sini...",
  });

  // Simpan instance Quill sebagai variabel global
  window.quill = quill;

  // Opsional: Debugging untuk melihat perubahan konten
  quill.on("text-change", function () {
    console.log("Konten Quill:", quill.root.innerHTML);
  });

  // Opsional: Jika ada konten yang perlu dimuat dari server
  // const savedContent = document.getElementById("savedContent").value;
  // if (savedContent) {
  //   quill.root.innerHTML = savedContent;
  // }
});
