<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduLab Universitas Halu Oleo</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
</head>
<body>

<div class="main-wrapper">
    <!-- Left Side: Branding Content -->
    <div class="branding-container">
        <div class="logos-group">
            <img src="/rpl/public/assets/images/mascot_owl.png" alt="EduLab Mascot Owl" class="mascot-img">
            <img src="/rpl/public/assets/images/logo_uho.png" alt="Logo Universitas Halu Oleo" class="uho-logo-img">
        </div>
        
        <h1 class="app-title">EduLab UHO</h1>
        <p class="app-subtitle">Sistem Manajemen Praktikum Terintegrasi Universitas Halu Oleo</p>
    </div>

    <!-- Right Side: Login Card -->
    <div class="card-container">
        <div class="login-card">
            <h2 class="form-title"><span class="title-edu">Edu</span><span class="title-lab">Lab</span></h2>

            <form action="/rpl/public/index.php?action=login" method="POST" class="login-form">
                <!-- NIM / NIP Field -->
                <div class="form-group">
                    <label for="identity" class="form-label">NIM/NIP</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <!-- User SVG Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </span>
                        <input type="text" name="identity" id="identity" class="form-input" placeholder="Masukkan NIM/NIP Anda" required autocomplete="username">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <!-- Lock SVG Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </span>
                        <input type="password" name="password" id="password" class="form-input password-input" placeholder="Masukkan Password Anda" required autocomplete="current-password">
                        <button type="button" id="password-toggle" class="password-toggle" aria-label="Tampilkan kata sandi">
                            <!-- Eye Open SVG Icon (default) -->
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Help Links Row -->
                <div class="form-options-row">
                    <label class="remember-me-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="help-link">Butuh bantuan?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggle = document.getElementById('password-toggle');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    // Password Visibility Toggle
    passwordToggle.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon representation
        if (type === 'text') {
            // Eye Closed SVG
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            passwordToggle.setAttribute('aria-label', 'Sembunyikan kata sandi');
        } else {
            // Eye Open SVG
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            passwordToggle.setAttribute('aria-label', 'Tampilkan kata sandi');
        }
    });
});
</script>
</body>
</html>
