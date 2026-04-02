<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= htmlspecialchars($title ?? 'Login') ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.5') ?>">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="<?= base_url() ?>">
                <span style="color: #3c8dbc;">Admin</span> Panel
            </a>
        </div>

        <div class="login-card">
            <div class="login-card-body">
                <p class="login-title">Sign in to your account</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem;">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem;">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form id="loginForm" action="<?= base_url('auth/login') ?>" method="post">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autocomplete="email">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </button>
                </form>
            </div>
        </div>

        <p style="text-align: center; margin-top: 1.5rem; color: rgba(255,255,255,0.6); font-size: 0.875rem;">
            &copy; <?= date('Y') ?> Admin Panel. All rights reserved.
        </p>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/custom.js?v=1.3') ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Form submission with AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;

                if (!email || !password) {
                    Toast.error('Please fill in all fields.');
                    return;
                }

                // Set loading state
                FormUtils.setLoading(loginBtn, true);

                // Submit via AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    FormUtils.setLoading(loginBtn, false);

                    if (data.success) {
                        Toast.success(data.message);
                        setTimeout(() => {
                            window.location.href = data.redirect || '<?= site_url('dashboard') ?>';
                        }, 500);
                    } else {
                        Toast.error(data.message || 'Login failed. Please try again.');
                    }
                })
                .catch(error => {
                    FormUtils.setLoading(loginBtn, false);
                    Toast.error('Network error. Please try again.');
                    console.error('Login error:', error);
                });
            });
        });
    </script>
</body>
</html>
