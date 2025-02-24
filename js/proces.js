// Add this script just before closing body tag
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Escape HTML to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Add loading spinner
    const spinner = `
        <div id="spinner" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
        </div>
    `;
    $('body').append(spinner);

    // Handle form submission
    $('form').on('submit', function(e) {
        e.preventDefault();
        
        // Show spinner
        $('#spinner').removeClass('hidden');
        
        // Clear previous error messages
        $('.error-message').remove();
        
        // Get and sanitize form data
        const formData = {
            username: escapeHtml($('#username').val().trim()),
            password: $('#password').val(),
            csrf_token: $('input[name="csrf_token"]').val()
        };

        // Client-side validation
        if (!formData.username || !formData.password) {
            showError('Please fill in all fields');
            return false;
        }

        // Basic input validation
        const invalidChars = /[<>(){}[\]'";]/;
        if (invalidChars.test(formData.username)) {
            showError('Invalid characters detected');
            return false;
        }

        // Send AJAX request
        $.ajax({
            url: 'login.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(xhr) {
                // Add CSRF header
                xhr.setRequestHeader('X-CSRF-Token', formData.csrf_token);
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Show success message (sanitized)
                    showNotification('success', escapeHtml(response.message));
                    
                    // Redirect after delay
                    setTimeout(function() {
                        window.location.href = escapeHtml(response.redirect);
                    }, 1500);
                } else {
                    showError(escapeHtml(response.message));
                }
            },
            error: function(xhr, status, error) {
                showError('An error occurred. Please try again later.');
                console.error('Error:', error);
            },
            complete: function() {
                // Hide spinner
                $('#spinner').addClass('hidden');
            }
        });
    });

    // Function to show error message (with XSS protection)
    function showError(message) {
        $('.error-message').remove();
        
        const errorHtml = `
            <div class="error-message text-red-500 text-sm mt-4 text-center">
                ${escapeHtml(message)}
            </div>
        `;
        $('form').append(errorHtml);
        
        $('#spinner').addClass('hidden');
    }

    // Function to show notification (with XSS protection)
    function showNotification(type, message) {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const notification = `
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded shadow-lg">
                ${escapeHtml(message)}
            </div>
        `;
        
        $('body').append(notification);
        
        setTimeout(function() {
            $('.notification').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script></script>