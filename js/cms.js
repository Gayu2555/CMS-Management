import Quill from "quill"; // Import Quill library

document.addEventListener("DOMContentLoaded", () => {
  var quill = new Quill("#editor", {
    theme: "snow",
    modules: {
      toolbar: false,
    },
  });

  // Custom toolbar functionality
  document.getElementById("bold-button").addEventListener("click", () => {
    quill.format("bold", !quill.getFormat().bold);
  });

  document.getElementById("italic-button").addEventListener("click", () => {
    quill.format("italic", !quill.getFormat().italic);
  });

  document.getElementById("underline-button").addEventListener("click", () => {
    quill.format("underline", !quill.getFormat().underline);
  });

  document
    .getElementById("bullet-list-button")
    .addEventListener("click", () => {
      quill.format("list", "bullet");
    });

  document
    .getElementById("ordered-list-button")
    .addEventListener("click", () => {
      quill.format("list", "ordered");
    });

  document.getElementById("image-button").addEventListener("click", () => {
    const url = prompt("Masukkan URL gambar:");
    if (url) {
      quill.insertEmbed(quill.getSelection().index, "image", url);
    }
  });

  document.getElementById("link-button").addEventListener("click", () => {
    const url = prompt("Masukkan URL:");
    if (url) {
      const range = quill.getSelection();
      if (range) {
        quill.format("link", url);
      }
    }
  });

  document.getElementById("text-color-button").addEventListener("click", () => {
    const color = prompt("Masukkan kode warna (contoh: #FF0000):");
    if (color) {
      quill.format("color", color);
    }
  });

  document
    .getElementById("font-size-select")
    .addEventListener("change", (e) => {
      if (e.target.value === "normal") {
        quill.format("header", false);
      } else {
        quill.format("header", e.target.value.replace("h", ""));
      }
    });

  // Read Mode functionality
  const readModeModal = document.getElementById("readModeModal");
  const readModeContent = document.getElementById("readModeContent");
  const closeReadMode = document.getElementById("closeReadMode");

  document.getElementById("read-mode-button").addEventListener("click", () => {
    readModeContent.innerHTML = quill.root.innerHTML;
    readModeModal.classList.remove("hidden");
    readModeModal.classList.add("flex");
  });

  closeReadMode.addEventListener("click", () => {
    readModeModal.classList.add("hidden");
    readModeModal.classList.remove("flex");
  });

  // Fungsi Baca Juga
  const readAlsoModal = document.getElementById("readAlsoModal");
  const readAlsoTitle = document.getElementById("readAlsoTitle");
  const readAlsoUrl = document.getElementById("readAlsoUrl");
  const cancelReadAlso = document.getElementById("cancelReadAlso");
  const insertReadAlso = document.getElementById("insertReadAlso");

  document.getElementById("read-also-button").addEventListener("click", () => {
    readAlsoModal.classList.remove("hidden");
    readAlsoModal.classList.add("flex");
  });

  cancelReadAlso.addEventListener("click", () => {
    readAlsoModal.classList.add("hidden");
    readAlsoModal.classList.remove("flex");
  });

  insertReadAlso.addEventListener("click", () => {
    const title = readAlsoTitle.value;
    const url = readAlsoUrl.value;
    if (title && url) {
      const readAlsoHtml = `<div class="read-also"><p><strong>Baca Juga:</strong> <a href="${url}" target="_blank">${title}</a></p></div>`;
      const range = quill.getSelection(true);
      quill.insertText(range.index, "\n");
      quill.clipboard.dangerouslyPasteHTML(range.index + 1, readAlsoHtml);
      quill.setSelection(range.index + 2);
      readAlsoModal.classList.add("hidden");
      readAlsoModal.classList.remove("flex");
      readAlsoTitle.value = "";
      readAlsoUrl.value = "";
    }
  });

  // Fungsi Dikutip Dari
  const quoteFromModal = document.getElementById("quoteFromModal");
  const quoteText = document.getElementById("quoteText");
  const quoteSource = document.getElementById("quoteSource");
  const cancelQuoteFrom = document.getElementById("cancelQuoteFrom");
  const insertQuoteFrom = document.getElementById("insertQuoteFrom");

  document.getElementById("quote-from-button").addEventListener("click", () => {
    quoteFromModal.classList.remove("hidden");
    quoteFromModal.classList.add("flex");
  });

  cancelQuoteFrom.addEventListener("click", () => {
    quoteFromModal.classList.add("hidden");
    quoteFromModal.classList.remove("flex");
  });

  insertQuoteFrom.addEventListener("click", () => {
    const text = quoteText.value;
    const source = quoteSource.value;
    if (text && source) {
      const quoteHtml = `<div class="quote-from"><p>Dikutip dari: <a href="#" data-source="${source}">"${text}"</a></p></div>`;
      const range = quill.getSelection(true);
      quill.insertText(range.index, "\n");
      quill.clipboard.dangerouslyPasteHTML(range.index + 1, quoteHtml);
      quill.setSelection(range.index + 2);
      quoteFromModal.classList.add("hidden");
      quoteFromModal.classList.remove("flex");
      quoteText.value = "";
      quoteSource.value = "";
    }
  });

  quill.root.addEventListener("click", (event) => {
    const anchor = event.target.closest("a[data-source]");
    if (anchor) {
      event.preventDefault();
      const source = anchor.getAttribute("data-source");
      alert("Sumber kutipan: " + source);
    }
  });
});
