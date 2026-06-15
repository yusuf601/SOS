<?php
// ==============================================================================
// EduLab UHO - Role Switcher View
// ==============================================================================

$name = $_SESSION['name'] ?? 'Guest';
$words = explode(" ", $name);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);
$availableRoles = $_SESSION['available_roles'] ?? [];
?>
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
                <?= htmlspecialchars($initials) ?>
            </div>
            <div class="profile-details">
                <h2 class="profile-name"><?= htmlspecialchars($name) ?></h2>
                <span class="profile-id"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
            </div>
        </div>

        <!-- Prompt Message -->
        <p class="switcher-prompt">
            Akun kamu memiliki <?= count($availableRoles) ?> peran. Pilih yang ingin digunakan:
        </p>

        <!-- Role Selection Stack -->
        <div class="role-options-stack">
            <?php if (in_array('Dosen', $availableRoles)): ?>
                <!-- Dosen Option -->
                <a href="/rpl/public/index.php?action=set_role&role=Dosen" class="role-option-card">
                    <div class="role-option-icon">
                        <!-- Book Open / Cap SVG Icon representing Teacher -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </div>
                    <span class="role-option-title">Dosen Pengampu</span>
                </a>
            <?php endif; ?>

            <?php if (in_array('Asisten', $availableRoles)): ?>
                <!-- Asisten Dosen Option -->
                <a href="/rpl/public/index.php?action=set_role&role=Asisten" class="role-option-card">
                    <div class="role-option-icon">
                        <!-- Shield/Briefcase/User-Check SVG Icon representing Assistant -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <span class="role-option-title">Asisten Dosen</span>
                </a>
            <?php endif; ?>

            <?php if (in_array('Mahasiswa', $availableRoles)): ?>
                <!-- Mahasiswa Option -->
                <a href="/rpl/public/index.php?action=set_role&role=Mahasiswa" class="role-option-card">
                    <div class="role-option-icon">
                        <!-- Graduation Cap SVG Icon representing Student -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 2 2.5 3 6 3s6-1 6-3v-5"></path></svg>
                    </div>
                    <span class="role-option-title">Mahasiswa</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
