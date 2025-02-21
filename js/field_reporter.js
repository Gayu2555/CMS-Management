// js/field_reporter.js
$(document).ready(function () {
  let currentPage = 1;
  let statusFilter = $("#statusFilter").val();

  // Initialize by loading reports
  loadReports(currentPage, statusFilter);

  // Handle status filter changes
  $("#statusFilter").on("change", function () {
    statusFilter = $(this).val();
    currentPage = 1; // Reset to first page when filter changes
    loadReports(currentPage, statusFilter);
  });

  // Handle pagination clicks
  $(document).on("click", ".pagination-link", function (e) {
    e.preventDefault();
    const page = $(this).data("page");
    loadReports(page, statusFilter);
  });

  function loadReports(page, status) {
    $.ajax({
      url: "backend/get_reports.php",
      type: "GET",
      dataType: "json",
      data: {
        ajax: true,
        page: page,
        status: status,
      },
      beforeSend: function () {
        // Show loading indicators
        $("#reportsTableBody").html(
          '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading data...</td></tr>'
        );
        $("#pagination").html(
          '<div class="flex justify-center w-full"><div class="animate-pulse bg-gray-200 h-8 w-64 rounded"></div></div>'
        );
      },
      success: function (response) {
        if (response.error) {
          $("#reportsTableBody").html(
            `<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">${response.error}</td></tr>`
          );
          $("#pagination").html("");
          return;
        }

        updateTable(response.reports);
        updatePagination(response.pagination);
        updateStatsCards(response.status_counts);
        updateTotalCount(response.pagination.total_reports);

        // Update current page
        currentPage = response.pagination.current_page;
      },
      error: function (xhr, status, error) {
        console.error("Ajax error:", error);
        let errorMessage = "Error loading data. Please try again.";

        if (xhr.responseJSON && xhr.responseJSON.error) {
          errorMessage = xhr.responseJSON.error;
        }

        $("#reportsTableBody").html(
          `<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">${errorMessage}</td></tr>`
        );
        $("#pagination").html("");
      },
    });
  }

  function updateTable(reports) {
    if (!reports || reports.length === 0) {
      $("#reportsTableBody").html(
        '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada laporan yang ditemukan</td></tr>'
      );
      return;
    }

    let html = "";
    reports.forEach(function (report) {
      let statusClass = getStatusClass(report.status);

      html += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${
                          report.who
                        }</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">${report.what}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">${report.where}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${report.when}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${capitalizeFirstLetter(report.status)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${report.photo_count} foto
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="report_detail.php?id=${
                          report.id
                        }" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                    </td>
                </tr>
            `;
    });

    $("#reportsTableBody").html(html);
  }

  function updatePagination(pagination) {
    if (!pagination || pagination.total_pages <= 1) {
      $("#pagination").html("");
      return;
    }

    let html = `
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">${
                          pagination.offset + 1
                        }</span>
                        to
                        <span class="font-medium">${Math.min(
                          pagination.offset + pagination.limit,
                          pagination.total_reports
                        )}</span>
                        of
                        <span class="font-medium">${
                          pagination.total_reports
                        }</span>
                        results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;

    // Previous button
    if (pagination.current_page > 1) {
      html += `
                <a href="#" data-page="${
                  pagination.current_page - 1
                }" class="pagination-link relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <i class="fas fa-chevron-left"></i>
                </a>
            `;
    }

    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(
      pagination.total_pages,
      pagination.current_page + 2
    );

    for (let i = startPage; i <= endPage; i++) {
      const isCurrentPage = i === pagination.current_page;
      const pageClass = isCurrentPage
        ? "bg-blue-50 text-blue-600 font-bold"
        : "bg-white text-gray-500 hover:bg-gray-50";

      html += `
                <a href="#" data-page="${i}" class="pagination-link relative inline-flex items-center px-4 py-2 border border-gray-300 ${pageClass} text-sm font-medium">
                    ${i}
                </a>
            `;
    }

    // Next button
    if (pagination.current_page < pagination.total_pages) {
      html += `
                <a href="#" data-page="${
                  pagination.current_page + 1
                }" class="pagination-link relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            `;
    }

    html += `
                    </nav>
                </div>
            </div>
        `;

    $("#pagination").html(html);
  }

  function updateStatsCards(statusCounts) {
    if (!statusCounts) {
      $("#statsCards").html("");
      return;
    }

    const statusConfig = {
      pending: {
        icon: "fa-clock",
        color: "bg-yellow-100 text-yellow-800 border-yellow-200",
      },
      reviewed: {
        icon: "fa-eye",
        color: "bg-blue-100 text-blue-800 border-blue-200",
      },
      approved: {
        icon: "fa-check-circle",
        color: "bg-green-100 text-green-800 border-green-200",
      },
      rejected: {
        icon: "fa-times-circle",
        color: "bg-red-100 text-red-800 border-red-200",
      },
    };

    let html = "";

    Object.keys(statusCounts).forEach((status) => {
      const count = statusCounts[status];
      const config = statusConfig[status] || {
        icon: "fa-question-circle",
        color: "bg-gray-100 text-gray-800 border-gray-200",
      };

      html += `
                <div class="${config.color} border p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full mr-4">
                            <i class="fas ${config.icon} text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium capitalize">${status}</p>
                            <p class="text-xl font-bold">${count}</p>
                        </div>
                    </div>
                </div>
            `;
    });

    $("#statsCards").html(html);
  }

  function updateTotalCount(total) {
    if (total !== undefined && total !== null) {
      $("#reportsCount").text(`Total: ${total} reports`);
    } else {
      $("#reportsCount").text(`Total: 0 reports`);
    }
  }

  function getStatusClass(status) {
    switch (status) {
      case "pending":
        return "bg-yellow-100 text-yellow-800";
      case "reviewed":
        return "bg-blue-100 text-blue-800";
      case "approved":
        return "bg-green-100 text-green-800";
      case "rejected":
        return "bg-red-100 text-red-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  }

  function capitalizeFirstLetter(string) {
    if (!string) return "";
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
});

// Add these functions to your field_reporter.js file

function viewPhoto(photoUrl) {
  // Create modal for viewing photo
  const modal = document.createElement("div");
  modal.className =
    "fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50";
  modal.id = "photoModal";

  modal.innerHTML = `
        <div class="bg-white rounded-lg max-w-3xl max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold">Photo Preview</h3>
                <button onclick="closePhotoModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 flex justify-center">
                <img src="${photoUrl}" alt="Report Photo" class="max-h-[70vh] max-w-full object-contain" />
            </div>
            <div class="p-4 border-t flex justify-end">
                <button onclick="closePhotoModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                    Close
                </button>
            </div>
        </div>
    `;

  document.body.appendChild(modal);
}

function closePhotoModal() {
  const modal = document.getElementById("photoModal");
  if (modal) {
    modal.remove();
  }
}

function updateStatus(reportId, newStatus) {
  // Show confirmation dialog before updating status
  if (!confirm(`Are you sure you want to mark this report as ${newStatus}?`)) {
    return;
  }

  $.ajax({
    url: "backend/update_report_status.php",
    type: "POST",
    data: {
      report_id: reportId,
      status: newStatus,
    },
    success: function (response) {
      try {
        const result = JSON.parse(response);
        if (result.success) {
          // Refresh the reports table
          loadReports();
          showNotification(`Report successfully ${newStatus}`, "success");
        } else {
          showNotification(`Error: ${result.message}`, "error");
        }
      } catch (e) {
        showNotification("Error processing response", "error");
        console.error(e);
      }
    },
    error: function () {
      showNotification("Server error while updating status", "error");
    },
  });
}

function deleteReport(reportId) {
  // Show confirmation dialog before deleting
  if (
    !confirm(
      "Are you sure you want to delete this report? This action cannot be undone."
    )
  ) {
    return;
  }

  $.ajax({
    url: "backend/delete_report.php",
    type: "POST",
    data: {
      report_id: reportId,
    },
    success: function (response) {
      try {
        const result = JSON.parse(response);
        if (result.success) {
          // Refresh the reports table
          loadReports();
          showNotification("Report successfully deleted", "success");
        } else {
          showNotification(`Error: ${result.message}`, "error");
        }
      } catch (e) {
        showNotification("Error processing response", "error");
        console.error(e);
      }
    },
    error: function () {
      showNotification("Server error while deleting report", "error");
    },
  });
}

function showNotification(message, type = "info") {
  const notificationContainer = document.getElementById(
    "notificationContainer"
  );

  if (!notificationContainer) {
    // Create the notification container if it doesn't exist
    const container = document.createElement("div");
    container.id = "notificationContainer";
    container.className =
      "fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-md";
    document.body.appendChild(container);
  }

  const notification = document.createElement("div");
  notification.className = `p-4 rounded-lg shadow-lg transition-opacity duration-300 flex items-center 
        ${
          type === "success"
            ? "bg-green-100 text-green-800 border-l-4 border-green-500"
            : type === "error"
            ? "bg-red-100 text-red-800 border-l-4 border-red-500"
            : "bg-blue-100 text-blue-800 border-l-4 border-blue-500"
        }`;

  notification.innerHTML = `
        <div class="flex-1">${message}</div>
        <button class="ml-4 text-gray-500 hover:text-gray-700" onclick="this.parentNode.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

  document.getElementById("notificationContainer").appendChild(notification);

  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    notification.classList.add("opacity-0");
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 5000);
}
