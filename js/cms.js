document.addEventListener("DOMContentLoaded", () => {
  // Inisialisasi Quill terlebih dahulu
  const editorContainer = document.getElementById("editor");
  let quill;

  if (editorContainer) {
    // Konfigurasi toolbar Quill
    const toolbarOptions = [
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      ["bold", "italic", "underline"],
      [{ list: "ordered" }, { list: "bullet" }],
      [{ color: [] }],
      ["link", "image"],
    ];

    // Inisialisasi Quill
    quill = new Quill("#editor", {
      theme: "snow",
      modules: {
        toolbar: false,
      },
      placeholder: "Tulis konten artikel Anda di sini...",
    });

    // Opsional: Debugging untuk melihat perubahan konten
    quill.on("text-change", function () {
      console.log("Konten Quill:", quill.root.innerHTML);
    });

    // Opsional: Jika ada konten yang perlu dimuat dari server
    // const savedContent = document.getElementById("savedContent").value;
    // if (savedContent) {
    //   quill.root.innerHTML = savedContent;
    // }
  } else {
    console.error(
      "Error: Elemen editor tidak ditemukan. Pastikan ada elemen dengan id 'editor'."
    );
    return; // Keluar dari fungsi jika editor tidak ditemukan
  }

  // Sekarang lanjutkan dengan kode cms.js setelah quill diinisialisasi

  // Initialize modal elements
  const modals = {
    readMode: document.getElementById("readModeModal"),
    readAlso: document.getElementById("readAlsoModal"),
    quoteFrom: document.getElementById("quoteFromModal"),
  };

  // Periksa apakah modal ada
  let modalsMissing = false;
  for (const [key, element] of Object.entries(modals)) {
    if (!element) {
      console.warn(`Modal ${key} tidak ditemukan dalam DOM.`);
      modalsMissing = true;
    }
  }

  if (modalsMissing) {
    console.warn(
      "Beberapa modal tidak ditemukan, fitur terkait mungkin tidak berfungsi."
    );
    // Tetap lanjutkan eksekusi, jangan return
  }

  const readModeContent = document.getElementById("readModeContent");
  if (!readModeContent && modals.readMode) {
    console.warn(
      "Element readModeContent tidak ditemukan, fitur read mode mungkin tidak berfungsi."
    );
  }

  // Get toolbar buttons
  const buttons = {
    bold: document.getElementById("bold-button"),
    italic: document.getElementById("italic-button"),
    underline: document.getElementById("underline-button"),
    bulletList: document.getElementById("bullet-list-button"),
    orderedList: document.getElementById("ordered-list-button"),
    textColor: document.getElementById("text-color-button"),
    image: document.getElementById("image-button"),
    link: document.getElementById("link-button"),
    readMode: document.getElementById("read-mode-button"),
    readAlso: document.getElementById("read-also-button"),
    quoteFrom: document.getElementById("quote-from-button"),
  };

  // Function to toggle format and update button state
  function toggleFormat(format) {
    const isFormatted = quill.getFormat()[format];
    quill.format(format, !isFormatted);
    updateButtonState(format, !isFormatted);
  }

  // Function to update button visual state
  function updateButtonState(format, isActive) {
    const button = buttons[format];
    if (button) {
      if (isActive) {
        button.classList.add("bg-gray-200");
        button.setAttribute("aria-pressed", "true");
      } else {
        button.classList.remove("bg-gray-200");
        button.setAttribute("aria-pressed", "false");
      }
    }
  }

  // Set up keyboard shortcuts
  const shortcuts = {
    b: "bold",
    i: "italic",
    u: "underline",
  };

  document.addEventListener("keydown", (e) => {
    if (e.ctrlKey || e.metaKey) {
      const format = shortcuts[e.key.toLowerCase()];
      if (format) {
        e.preventDefault();
        toggleFormat(format);
      }
    } else if (e.key === "Escape") {
      Object.values(modals).forEach((modal) => {
        if (modal) {
          modal.classList.replace("flex", "hidden");
        }
      });
    }
  });

  // Add click listeners for basic formatting
  ["bold", "italic", "underline"].forEach((format) => {
    if (buttons[format]) {
      buttons[format].addEventListener("click", () => toggleFormat(format));
    }
  });

  // List formatting
  if (buttons.bulletList) {
    buttons.bulletList.addEventListener("click", () =>
      quill.format("list", "bullet")
    );
  }
  if (buttons.orderedList) {
    buttons.orderedList.addEventListener("click", () =>
      quill.format("list", "ordered")
    );
  }

  // Text color
  if (buttons.textColor) {
    buttons.textColor.addEventListener("click", () => {
      const color = prompt("Masukkan kode warna (contoh: #FF0000):");
      if (color) quill.format("color", color);
    });
  }

  // Font size handling
  const fontSizeSelect = document.getElementById("font-size-select");
  if (fontSizeSelect) {
    fontSizeSelect.addEventListener("change", (e) => {
      const value = e.target.value;
      quill.format(
        "header",
        value === "normal" ? false : parseInt(value.replace("h", ""))
      );
    });
  }

  // Image handling
  if (buttons.image) {
    buttons.image.addEventListener("click", () => {
      const url = prompt("Masukkan URL gambar:");
      if (url) {
        const range = quill.getSelection() || {
          index: quill.getLength(),
        };
        quill.insertEmbed(range.index, "image", url);
      }
    });
  }

  // Link handling
  if (buttons.link) {
    buttons.link.addEventListener("click", () => {
      const url = prompt("Masukkan URL:");
      if (url) {
        const range = quill.getSelection();
        if (range) {
          if (range.length > 0) {
            quill.format("link", url);
          } else {
            const text = prompt("Masukkan teks tautan:");
            if (text) {
              quill.insertText(range.index, text, {
                link: url,
              });
            }
          }
        }
      }
    });
  }

  // Read mode functionality
  if (buttons.readMode && readModeContent && modals.readMode) {
    buttons.readMode.addEventListener("click", () => {
      readModeContent.innerHTML = quill.root.innerHTML;
      modals.readMode.classList.replace("hidden", "flex");
    });

    // Close button for read mode
    const closeReadMode = document.getElementById("closeReadMode");
    if (closeReadMode) {
      closeReadMode.addEventListener("click", () => {
        modals.readMode.classList.replace("flex", "hidden");
      });
    }
  }

  // Read Also functionality
  if (buttons.readAlso && modals.readAlso) {
    buttons.readAlso.addEventListener("click", () => {
      modals.readAlso.classList.replace("hidden", "flex");
    });

    // Cancel Read Also
    const cancelReadAlso = document.getElementById("cancelReadAlso");
    if (cancelReadAlso) {
      cancelReadAlso.addEventListener("click", () => {
        const readAlsoTitle = document.getElementById("readAlsoTitle");
        const readAlsoUrl = document.getElementById("readAlsoUrl");
        if (readAlsoTitle) readAlsoTitle.value = "";
        if (readAlsoUrl) readAlsoUrl.value = "";
        modals.readAlso.classList.replace("flex", "hidden");
      });
    }

    // Insert Read Also
    const insertReadAlso = document.getElementById("insertReadAlso");
    if (insertReadAlso) {
      insertReadAlso.addEventListener("click", () => {
        const readAlsoTitle = document.getElementById("readAlsoTitle");
        const readAlsoUrl = document.getElementById("readAlsoUrl");

        if (!readAlsoTitle || !readAlsoUrl) {
          console.error(
            "Element readAlsoTitle atau readAlsoUrl tidak ditemukan."
          );
          return;
        }

        const title = readAlsoTitle.value.trim();
        const url = readAlsoUrl.value.trim();

        if (!title || !url) {
          alert("Harap isi judul dan URL artikel");
          return;
        }

        const html = `
          <div class="read-also p-4 bg-gray-50 rounded-lg my-4">
            <p><strong>Baca Juga:</strong> <a href="${url}" target="_blank" class="text-blue-600 hover:underline">${title}</a></p>
          </div>
        `;

        const range = quill.getSelection(true);
        quill.clipboard.dangerouslyPasteHTML(range.index, html);

        // Reset form dan tutup modal
        readAlsoTitle.value = "";
        readAlsoUrl.value = "";
        modals.readAlso.classList.replace("flex", "hidden");
      });
    }
  }

  // Quote From functionality
  if (buttons.quoteFrom && modals.quoteFrom) {
    buttons.quoteFrom.addEventListener("click", () => {
      modals.quoteFrom.classList.replace("hidden", "flex");
    });

    // Get quote elements
    const quoteText = document.getElementById("quoteText");
    const quoteSource = document.getElementById("quoteSource");
    const cancelQuoteFrom = document.getElementById("cancelQuoteFrom");
    const insertQuoteFrom = document.getElementById("insertQuoteFrom");

    // Handle modal close
    if (cancelQuoteFrom) {
      cancelQuoteFrom.addEventListener("click", () => {
        // Clear form fields
        if (quoteText) quoteText.value = "";
        if (quoteSource) quoteSource.value = "";
        modals.quoteFrom.classList.replace("flex", "hidden");
      });
    }

    // Handle citation insertion
    if (insertQuoteFrom && quoteText && quoteSource) {
      insertQuoteFrom.addEventListener("click", () => {
        const text = quoteText.value.trim();
        const source = quoteSource.value.trim();

        if (!text || !source) {
          alert("Harap isi teks kutipan dan sumber");
          return;
        }

        // Create citation HTML
        const citationHtml = `
          <div class="citation-wrapper p-4 bg-gray-50 border rounded-lg my-4">
            <div class="citation-text text-gray-700">
              <span class="font-medium">Dikutip dari:</span> 
              <a href="#" 
                data-source="${source}" 
                class="text-blue-600 hover:text-blue-800"
              >${text}</a>
            </div>
          </div>
        `;

        // Insert into editor
        const range = quill.getSelection(true);
        quill.insertText(range.index, "\n");
        quill.clipboard.dangerouslyPasteHTML(range.index + 1, citationHtml);
        quill.setSelection(range.index + 2);

        // Reset and close modal
        quoteText.value = "";
        quoteSource.value = "";
        modals.quoteFrom.classList.replace("flex", "hidden");
      });
    }

    // Handle citation source click
    quill.root.addEventListener("click", (event) => {
      const citationLink = event.target.closest("a[data-source]");
      if (citationLink) {
        event.preventDefault();
        const source = citationLink.getAttribute("data-source");
        alert(`Sumber kutipan: ${source}`);
      }
    });
  }

  // Close modals when clicking outside
  Object.values(modals).forEach((modal) => {
    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          // Reset forms
          if (modal === modals.readAlso) {
            const readAlsoTitle = document.getElementById("readAlsoTitle");
            const readAlsoUrl = document.getElementById("readAlsoUrl");
            if (readAlsoTitle) readAlsoTitle.value = "";
            if (readAlsoUrl) readAlsoUrl.value = "";
          } else if (modal === modals.quoteFrom) {
            const quoteText = document.getElementById("quoteText");
            const quoteSource = document.getElementById("quoteSource");
            if (quoteText) quoteText.value = "";
            if (quoteSource) quoteSource.value = "";
          }
          modal.classList.replace("flex", "hidden");
        }
      });
    }
  });

  // Set up MutationObserver untuk memantau perubahan konten
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "childList") {
        const formats = quill.getFormat();
        ["bold", "italic", "underline"].forEach((format) => {
          updateButtonState(format, formats[format]);
        });
      }
    });
  });

  // Konfigurasi observer
  const config = {
    childList: true,
    subtree: true,
    characterData: true,
  };

  // Mulai mengamati perubahan pada editor
  observer.observe(quill.root, config);

  // Update button states when selection changes
  quill.on("selection-change", (range) => {
    if (range) {
      const formats = quill.getFormat(range);
      ["bold", "italic", "underline"].forEach((format) => {
        updateButtonState(format, formats[format]);
      });
    }
  });

  // Add hover effects to formatting buttons
  ["bold", "italic", "underline"].forEach((format) => {
    const button = buttons[format];
    if (button) {
      button.addEventListener("mouseenter", () => {
        if (button.getAttribute("aria-pressed") !== "true") {
          button.classList.add("hover:bg-gray-200");
        }
      });

      button.addEventListener("mouseleave", () => {
        if (button.getAttribute("aria-pressed") !== "true") {
          button.classList.remove("hover:bg-gray-200");
        }
      });
    }
  });

  // Cleanup when page is unloaded
  window.addEventListener("unload", () => {
    observer.disconnect();
  });

  // Tambahkan event listener untuk form submission
  const articleForm = document.getElementById("articleForm");
  if (articleForm) {
    articleForm.addEventListener("submit", (e) => {
      const hiddenContent = document.getElementById("hiddenContent");
      if (hiddenContent) {
        hiddenContent.value = quill.root.innerHTML;
      }

      const requiredFields = [
        "title",
        "author",
        "category",
        "date_created",
        "position",
      ];

      const isEmpty = requiredFields.some((id) => {
        const field = document.getElementById(id);
        return field && !field.value;
      });

      if (isEmpty) {
        alert("Harap isi semua kolom wajib");
        e.preventDefault();
        return false;
      }
      return true;
    });
  }
});

// Bagian untuk image preview dan validation
document.addEventListener("DOMContentLoaded", function () {
  const imageInput = document.getElementById("image");
  if (!imageInput) return;

  const imagePreview = document.getElementById("imagePreview");
  if (!imagePreview) return;

  const previewImage =
    imagePreview.querySelector("img") || document.createElement("img");
  const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes

  // Function to show error message
  function showError(message) {
    const existingError = document.querySelector(".error-message");
    if (existingError) existingError.remove();

    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message text-red-500 text-sm mt-2";
    errorDiv.textContent = message;
    imagePreview.parentNode.appendChild(errorDiv);
  }

  // Function to clear error message
  function clearError() {
    const existingError = document.querySelector(".error-message");
    if (existingError) existingError.remove();
  }

  // Function to validate image
  function validateImage(file) {
    // Check file size
    if (file.size > maxFileSize) {
      showError("Ukuran file terlalu besar. Maksimal 2MB.");
      imageInput.value = "";
      return false;
    }

    // Check file type
    const allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    if (!allowedTypes.includes(file.type)) {
      showError(
        "Format file tidak didukung. Gunakan JPEG, PNG, GIF, atau WEBP."
      );
      imageInput.value = "";
      return false;
    }

    return true;
  }

  // Handle image selection
  imageInput.addEventListener("change", function (e) {
    clearError();
    const file = e.target.files[0];

    if (file) {
      if (validateImage(file)) {
        const reader = new FileReader();

        reader.onload = function (e) {
          previewImage.src = e.target.result;
          previewImage.alt = "Preview";
          previewImage.className = "max-w-xs mx-auto rounded-lg shadow-md";

          if (!imagePreview.contains(previewImage)) {
            imagePreview.appendChild(previewImage);
          }

          imagePreview.classList.remove("hidden");
        };

        reader.readAsDataURL(file);
      }
    } else {
      imagePreview.classList.add("hidden");
    }
  });

  // Menambahkan validasi pada form submission
  const form = document.getElementById("articleForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      if (!imageInput.files || !imageInput.files[0]) {
        e.preventDefault();
        showError("Silakan pilih gambar utama artikel.");
        return;
      }

      const figcaption = document.getElementById("figcaption");
      if (figcaption && !figcaption.value.trim()) {
        e.preventDefault();
        showError("Silakan isi keterangan gambar.");
        return;
      }
    });
  }
});
