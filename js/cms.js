document.addEventListener("DOMContentLoaded", () => {
  // Initialize Quill
  const quill = new Quill("#editor", {
    theme: "snow",
    modules: {
      toolbar: false,
    },
  });

  // Initialize modal elements
  const modals = {
    readMode: document.getElementById("readModeModal"),
    readAlso: document.getElementById("readAlsoModal"),
    quoteFrom: document.getElementById("quoteFromModal"),
  };
  const readModeContent = document.getElementById("readModeContent");

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
        modal.classList.replace("flex", "hidden");
      });
    }
  });

  // Add click listeners for basic formatting
  ["bold", "italic", "underline"].forEach((format) => {
    buttons[format]?.addEventListener("click", () => toggleFormat(format));
  });

  // List formatting
  buttons.bulletList?.addEventListener("click", () =>
    quill.format("list", "bullet")
  );
  buttons.orderedList?.addEventListener("click", () =>
    quill.format("list", "ordered")
  );

  // Text color
  buttons.textColor?.addEventListener("click", () => {
    const color = prompt("Masukkan kode warna (contoh: #FF0000):");
    if (color) quill.format("color", color);
  });

  // Font size handling
  document
    .getElementById("font-size-select")
    ?.addEventListener("change", (e) => {
      const value = e.target.value;
      quill.format(
        "header",
        value === "normal" ? false : parseInt(value.replace("h", ""))
      );
    });

  // Image handling
  buttons.image?.addEventListener("click", () => {
    const url = prompt("Masukkan URL gambar:");
    if (url) {
      const range = quill.getSelection() || {
        index: quill.getLength(),
      };
      quill.insertEmbed(range.index, "image", url);
    }
  });

  // Link handling
  buttons.link?.addEventListener("click", () => {
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

  // Read mode functionality
  buttons.readMode?.addEventListener("click", () => {
    readModeContent.innerHTML = quill.root.innerHTML;
    modals.readMode.classList.replace("hidden", "flex");
  });

  // Close button for read mode
  document.getElementById("closeReadMode")?.addEventListener("click", () => {
    modals.readMode.classList.replace("flex", "hidden");
  });

  // Read Also functionality
  buttons.readAlso?.addEventListener("click", () => {
    modals.readAlso.classList.replace("hidden", "flex");
  });

  // Cancel Read Also
  document.getElementById("cancelReadAlso")?.addEventListener("click", () => {
    document.getElementById("readAlsoTitle").value = "";
    document.getElementById("readAlsoUrl").value = "";
    modals.readAlso.classList.replace("flex", "hidden");
  });

  // Insert Read Also
  document.getElementById("insertReadAlso")?.addEventListener("click", () => {
    const title = document.getElementById("readAlsoTitle").value.trim();
    const url = document.getElementById("readAlsoUrl").value.trim();

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
    document.getElementById("readAlsoTitle").value = "";
    document.getElementById("readAlsoUrl").value = "";
    modals.readAlso.classList.replace("flex", "hidden");
  });

  // Quote From button and modal handling
  buttons.quoteFrom?.addEventListener("click", () => {
    modals.quoteFrom.classList.replace("hidden", "flex");
  });

  // Cancel Quote
  document.getElementById("cancelQuoteFrom")?.addEventListener("click", () => {
    document.getElementById("quoteText").value = "";
    document.getElementById("quoteSource").value = "";
    modals.quoteFrom.classList.replace("flex", "hidden");
  });

  // Insert Quote
  // Get modal elements
  const quoteFromModal = document.getElementById("quoteFromModal");
  const quoteText = document.getElementById("quoteText");
  const quoteSource = document.getElementById("quoteSource");
  const cancelQuoteFrom = document.getElementById("cancelQuoteFrom");
  const insertQuoteFrom = document.getElementById("insertQuoteFrom");

  // Open modal when citation button is clicked
  document
    .getElementById("quote-from-button")
    ?.addEventListener("click", () => {
      quoteFromModal.classList.replace("hidden", "flex");
    });

  // Handle modal close
  cancelQuoteFrom?.addEventListener("click", () => {
    // Clear form fields
    quoteText.value = "";
    quoteSource.value = "";
    quoteFromModal.classList.replace("flex", "hidden");
  });

  // Handle citation insertion
  insertQuoteFrom?.addEventListener("click", () => {
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
    quoteFromModal.classList.replace("flex", "hidden");
  });

  // Handle citation source click
  quill.root.addEventListener("click", (event) => {
    const citationLink = event.target.closest("a[data-source]");
    if (citationLink) {
      event.preventDefault();
      const source = citationLink.getAttribute("data-source");
      alert(`Sumber kutipan: ${source}`);
    }
  });
  // Reset form dan tutup modal
  document.getElementById("quoteText").value = "";
  document.getElementById("quoteSource").value = "";
  modals.quoteFrom.classList.replace("flex", "hidden");
});

// Close modals when clicking outside
Object.values(modals).forEach((modal) => {
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      // Reset forms
      if (modal === modals.readAlso) {
        document.getElementById("readAlsoTitle").value = "";
        document.getElementById("readAlsoUrl").value = "";
      } else if (modal === modals.quoteFrom) {
        document.getElementById("quoteText").value = "";
        document.getElementById("quoteSource").value = "";
      }
      modal.classList.replace("flex", "hidden");
    }
  });
});

// Escape key to close modals
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    Object.values(modals).forEach((modal) => {
      if (modal.classList.contains("flex")) {
        // Reset forms
        if (modal === modals.readAlso) {
          document.getElementById("readAlsoTitle").value = "";
          document.getElementById("readAlsoUrl").value = "";
        } else if (modal === modals.quoteFrom) {
          document.getElementById("quoteText").value = "";
          document.getElementById("quoteSource").value = "";
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

// Set up modal event listeners for clicking outside
Object.values(modals).forEach((modal) => {
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.classList.replace("flex", "hidden");
    }
  });
});

// Form submission handling
const articleForm = document.getElementById("articleForm");
if (articleForm) {
  articleForm.addEventListener("submit", (e) => {
    document.getElementById("hiddenContent").value = quill.root.innerHTML;
    const requiredFields = [
      "title",
      "author",
      "category",
      "date_created",
      "position",
    ];
    const isEmpty = requiredFields.some(
      (id) => !document.getElementById(id)?.value
    );
    if (isEmpty) {
      alert("Harap isi semua kolom wajib");
      e.preventDefault();
      return false;
    }
    return true;
  });
}

// Cleanup when page is unloaded
window.addEventListener("unload", () => {
  observer.disconnect();
});
