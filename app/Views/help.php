<?php
// ==============================================================================
// EduLab UHO - Butuh Bantuan View
// ==============================================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Butuh Bantuan? - EduLab UHO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-image: url('/rpl/public/assets/images/bg_campus.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Latar belakang dengan overlay */
        .help-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.65); /* Darker overlay to match design */
            z-index: 1;
        }

        /* Modal Utama */
        .help-modal {
            position: relative;
            z-index: 2;
            width: 1081px;
            max-width: 95%;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            background-color: #FFFFFF;
            border: 1px solid #2A4D88;
        }

        /* Bagian Atas Biru */
        .help-header {
            background-color: #364087;
            height: 169px;
            padding: 40px 68px;
            box-sizing: border-box;
            color: #FFFFFF;
        }

        .help-title {
            font-size: 40px;
            font-weight: 600;
            margin: 0 0 10px 0;
            line-height: 1.2;
        }

        .help-subtitle {
            font-size: 20px;
            font-weight: 500;
            margin: 0;
            line-height: 1.2;
            opacity: 0.9;
        }

        /* Bagian Bawah Putih */
        .help-body {
            background-color: #FFFFFF;
            padding: 36px 44px;
            box-sizing: border-box;
        }

        /* Banner Lupa Password */
        .banner-password {
            background-color: #29316B;
            border-radius: 15px;
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #FFFFFF;
            margin-bottom: 50px;
        }

        .banner-password-left h3 {
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }

        .banner-password-left p {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
            opacity: 0.9;
        }

        /* Tombol WhatsApp */
        .btn-wa {
            background-color: #25D366;
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 600;
            width: 205px;
            height: 52px;
            border-radius: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-sizing: border-box;
            transition: opacity 0.2s;
        }

        .btn-wa:hover {
            opacity: 0.9;
        }

        /* Kartu Info Bawah */
        .info-cards {
            display: flex;
            gap: 50px;
            justify-content: center;
        }

        .info-card {
            width: 307px;
            height: 140px;
            border-radius: 15px;
            padding: 16px 22px;
            box-sizing: border-box;
            position: relative;
        }

        .info-card-left {
            background-color: rgba(37, 211, 102, 0.17); /* #25D3662B */
            border: 1px solid #25D366;
            color: #167D3C;
        }

        .info-card-right {
            background-color: #FFFFFF;
            border: 1px solid #7C94B8;
            color: #000000;
        }

        .info-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card-right .info-title {
            font-size: 15px;
            margin-bottom: 20px;
        }

        .info-desc {
            font-size: 11px;
            font-weight: 500;
            margin: 0 0 8px 0;
        }

        .info-detail {
            font-size: 9px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .contact-item {
            font-size: 11px;
            font-weight: 500;
            color: #7397B3;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .btn-wa-small {
            background-color: #25D366;
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 600;
            width: 266px;
            height: 27px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: absolute;
            bottom: 16px;
            left: 20px;
            box-sizing: border-box;
        }
        
        .btn-back-login {
            position: absolute;
            top: 40px;
            right: 40px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .btn-back-login:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>

    <div class="help-bg"></div>

    <div class="help-modal">
        <a href="/rpl/public/index.php" class="btn-back-login">Kembali ke Login</a>
        <div class="help-header">
            <h1 class="help-title">Butuh Bantuan?</h1>
            <p class="help-subtitle">Temukan jawaban dari pertanyaan umum, atau hubungi admin secara langsung.</p>
        </div>
        
        <div class="help-body">
            <div class="banner-password">
                <div class="banner-password-left">
                    <h3>Lupa password?</h3>
                    <p>Hubungi admin via WhatsApp dengan menyertakan nama lengkap dan NIM/NIP kamu.</p>
                </div>
                <a href="https://wa.me/6281234567890" target="_blank" class="btn-wa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    Hubungi Admin
                </a>
            </div>

            <div class="info-cards">
                <!-- Chat Admin via Whatsapp -->
                <div class="info-card info-card-left">
                    <h4 class="info-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        Chat Admin via Whatsapp
                    </h4>
                    <p class="info-desc">Belum menemukan jawaban? Hubungi admin lab langsung.</p>
                    <div class="info-detail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Senin–Jumat, 08.00–16.00 WITA
                    </div>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn-wa-small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        Hubungi Admin
                    </a>
                </div>

                <!-- Informasi Kontak -->
                <div class="info-card info-card-right">
                    <h4 class="info-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8161BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        Informasi Kontak
                    </h4>
                    <div class="contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <a href="mailto:edulab@uho.ac.id" style="color: #7397B3;">edulab@uho.ac.id</a>
                    </div>
                    <div class="contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        (0401) 000-0000
                    </div>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn-wa-small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        Hubungi Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
