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
        updateTable(response.reports);
        updatePagination(response.pagination);
        updateStatsCards(response.status_counts);
        updateTotalCount(response.pagination.total_reports);

        // Update current page
        currentPage = response.pagination.current_page;
      },
      error: function () {
        $("#reportsTableBody").html(
          '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading data. Please try again.</td></tr>'
        );
        $("#pagination").html("");
      },
    });
  }

  function updateTable(reports) {
    if (reports.length === 0) {
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
    if (pagination.total_pages <= 1) {
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
      const config = statusConfig[status];

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
    $("#reportsCount").text(`Total: ${total} reports`);
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
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
});
