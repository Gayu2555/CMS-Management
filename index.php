<?php
session_start();

// Buat CSRF token jika belum ada
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Family -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="min-h-screen w-full bg-[#080710] font-[Poppins]">
    <!-- Background shapes -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute -left-20 -top-20 h-[200px] w-[200px] rounded-full bg-gradient-to-b from-[#1845ad] to-[#23a2f6]"></div>
        <div class="absolute -right-8 -bottom-20 h-[200px] w-[200px] rounded-full bg-gradient-to-r from-[#ff512f] to-[#f09819]"></div>
    </div>

    <!-- Form -->
    <div class="min-h-screen w-full flex items-center justify-center p-4">
        <div class="w-full max-w-[400px]">
            <form class="backdrop-blur-md bg-white/[0.13] rounded-lg border border-white/10 shadow-[0_0_40px_rgba(8,7,16,0.6)] p-8 md:p-10">
                <h3 class="text-3xl font-medium text-white text-center mb-8">
                    Login Here
                </h3>
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label for="username" class="text-white block font-medium">
                            Username
                        </label>
                        <input
                            type="text"
                            id="username"
                            placeholder="Email or Phone"
                            class="w-full h-12 px-3 rounded-md bg-white/[0.07] border-0 text-white placeholder:text-gray-300 outline-none" />
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-white block font-medium">
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            placeholder="Password"
                            class="w-full h-12 px-3 rounded-md bg-white/[0.07] border-0 text-white placeholder:text-gray-300 outline-none" />
                    </div>

                    <button type="submit" class="w-full h-12 bg-white hover:bg-gray-100 text-[#080710] text-lg font-semibold rounded-md transition-colors">
                        Log In
                    </button>

                    <div class="flex gap-4 mt-6">
                        <div class="flex-1 flex items-center justify-center gap-2 py-2 px-4 bg-white/[0.27] hover:bg-white/[0.47] text-white rounded-md cursor-pointer transition-colors">
                            <i class="fab fa-google"></i>
                            Google
                        </div>
                        <div class="flex-1 flex items-center justify-center gap-2 py-2 px-4 bg-white/[0.27] hover:bg-white/[0.47] text-white rounded-md cursor-pointer transition-colors">
                            <i class="fab fa-facebook"></i>
                            Facebook
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                    url: 'backend/login.php',
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
    </script>
</body>

</html>