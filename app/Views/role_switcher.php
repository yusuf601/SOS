<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
</head>
<body class="bg-campus-theme">

<div class="switcher-wrapper">
    <div class="switcher-card">
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                JD
            </div>
            <div class="profile-details">
                <h2 class="profile-name">John Doe</h2>
                <span class="profile-id">E1E122001</span>
            </div>
        </div>

        <!-- Prompt Message -->
        <p class="switcher-prompt">
            Akun kamu memiliki 2 peran. Pilih yang ingin digunakan:
        </p>

        <!-- Role Selection Stack -->
        <div class="role-options-stack">
            <!-- Asisten Dosen Option -->
            <a href="/rpl/public/index.php?action=set_role&role=Asisten" class="role-option-card">
                <div class="role-option-icon">
                    <!-- Shield/Briefcase/User-Check SVG Icon representing Assistant -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <span class="role-option-title">Asisten Dosen</span>
            </a>

            <!-- Mahasiswa Option -->
            <a href="/rpl/public/index.php?action=set_role&role=Mahasiswa" class="role-option-card">
                <div class="role-option-icon">
                    <!-- Graduation Cap SVG Icon representing Student -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 2 2.5 3 6 3s6-1 6-3v-5"></path></svg>
                </div>
                <span class="role-option-title">Mahasiswa</span>
            </a>
        </div>
    </div>
</div>

</body>
</html>
