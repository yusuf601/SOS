<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Modul - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Bank Modul layout */
        .class-selector-card {
            background-color: var(--text-color-light);
            border-radius: 15px;
            padding: 24px;
            border: 0.5px solid #DCDCDC;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }

        .class-selector-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .class-selector-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        .custom-select-wrapper {
            position: relative;
            width: 320px;
            max-width: 100%;
        }

        .class-select {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            background-color: #F8FAFC;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-color-dark);
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            transition: all var(--transition-speed) ease;
        }

        .class-select:focus {
            border-color: var(--btn-primary);
            box-shadow: 0 0 0 3px rgba(54, 64, 135, 0.1);
        }

        .custom-select-wrapper::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-top-color: var(--text-color-dark);
            pointer-events: none;
        }

        .class-meta-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding-top: 16px;
            border-top: 1px solid #F3F4F6;
        }

        .meta-info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-info-label {
            font-size: 12px;
            font-weight: 600;
            color: #9B9B9B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-info-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        /* Modul Cards */
        .moduls-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modul-card {
            background-color: var(--text-color-light);
            border-radius: 15px;
            border: 0.5px solid #DCDCDC;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all var(--transition-speed) ease;
        }

        .modul-card:hover {
            transform: translateY(-2px);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.04);
        }

        .modul-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background-color: #F8FAFC;
            border-bottom: 0.5px solid #E3E3E3;
            flex-wrap: wrap;
            gap: 16px;
        }

        .modul-identity {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .modul-number-badge {
            background-color: var(--btn-primary);
            color: var(--text-color-light);
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0px 4px 8px rgba(54, 64, 135, 0.2);
        }

        .modul-title-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .modul-title {
            font-size: 18px;
            font-weight: 600;
            color: #000000;
        }

        .modul-date {
            font-size: 13px;
            color: #9B9B9B;
            font-weight: 500;
        }

        .modul-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .modul-description {
            font-size: 15px;
            color: #475569;
            line-height: 1.6;
            font-weight: 500;
        }

        .download-actions-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
            border-top: 1px solid #F1F5F9;
            padding-top: 16px;
        }

        .modul-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .btn-materi {
            background-color: rgba(54, 64, 135, 0.1);
            color: var(--btn-primary);
        }

        .btn-materi:hover {
            background-color: var(--btn-primary);
            color: var(--text-color-light);
        }

        .btn-petunjuk {
            background-color: rgba(219, 36, 30, 0.08);
            color: #DB241E;
        }

        .btn-petunjuk:hover {
            background-color: #DB241E;
            color: var(--text-color-light);
        }

        /* Toast Styling */
        .toast {
            visibility: hidden;
            min-width: 280px;
            background-color: #1E293B;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px;
            position: fixed;
            z-index: 150;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @keyframes fadein {
            from { bottom: 0; opacity: 0; }
            to { bottom: 30px; opacity: 1; }
        }

        @keyframes fadeout {
            from { bottom: 30px; opacity: 1; }
            to { bottom: 0; opacity: 0; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <!-- Sidebar Brand -->
            <div class="sidebar-header">
                <img src="/rpl/public/assets/images/logo_uho.png" alt="Logo UHO" class="sidebar-logo">
                <span class="sidebar-brand-name">EduLab</span>
            </div>

            <!-- User profile summary -->
            <div class="sidebar-user-card">
                <div class="sidebar-user-name">John Doe</div>
                <div class="sidebar-user-role">Mahasiswa</div>
            </div>

            <!-- Menu Navigation -->
            <nav>
                <div class="sidebar-menu-title">Menu Utama</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item">
                        <a href="/rpl/public/index.php?action=dashboard_student">
                            <span>Dashboard</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="/rpl/public/index.php?action=my_classes">
                            <span>Kelas Saya</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            </span>
                        </a>
                        <ul class="sidebar-submenu-list">
                            <li class="sidebar-submenu-item active">
                                <a href="/rpl/public/index.php?action=bank_modul">
                                    <span>Bank Modul</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="/rpl/public/index.php?action=upload_tugas">
                            <span>Upload Tugas</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
                            <span>Lihat Nilai</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
                            <span>Data Presensi</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
                            <span>Sanggah Nilai</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
                            <span>Status Kelulusan</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div>
            <div class="sidebar-divider"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="#">
                        <span>Pengaturan</span>
                        <span class="sidebar-menu-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/rpl/public/index.php?action=logout" style="color: #FF8A8A;">
                        <span>Keluar</span>
                        <span class="sidebar-menu-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="main-workspace">
        <!-- Top Navbar -->
        <header class="workspace-navbar">
            <h2 class="navbar-title">Bank Modul</h2>
            <div class="navbar-profile">
                <!-- User Notification Button -->
                <button type="button" style="background:none; border:none; color:white; cursor:pointer;" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                
                <div class="navbar-avatar">JD</div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            <!-- Class Selector -->
            <section class="class-selector-card">
                <div class="class-selector-header">
                    <span class="class-selector-title">Pilih Kelas Praktikum:</span>
                    <div class="custom-select-wrapper">
                        <select class="class-select" id="classSelector" aria-label="Pilih kelas praktikum">
                            <option value="web" selected>Praktikum Pemrograman Web (Kelas A)</option>
                            <option value="database">Praktikum Basis Data (Kelas B)</option>
                            <option value="network">Praktikum Jaringan Komputer (Kelas C)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Class Meta Details -->
                <div class="class-meta-info">
                    <div class="meta-info-item">
                        <span class="meta-info-label">Dosen Pengampu</span>
                        <span class="meta-info-value" id="lecturerName">Prof. Albert Wesker</span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Asisten Praktikum</span>
                        <span class="meta-info-value" id="assistantName">Chris Redfield</span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Jadwal & Ruang</span>
                        <span class="meta-info-value" id="classSchedule">Senin 08:00 - 10:00 (Lab Komputer 2)</span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Kelompok Kamu</span>
                        <span class="meta-info-value" id="groupName">Kelompok 1 (Kel. 1)</span>
                    </div>
                </div>
            </section>

            <!-- Moduls list -->
            <section class="moduls-container" id="modulsContainer">
                <!-- Modules will be populated by Javascript -->
            </section>
        </div>
    </main>
</div>

<!-- Success Toast Notification -->
<div id="toastNotification" class="toast">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
    <span id="toastMessage">Materi berhasil diunduh!</span>
</div>

<script>
    // Class metadata switcher logic (to simulate DB querying)
    const classData = {
        web: {
            lecturer: "Prof. Albert Wesker",
            assistant: "Chris Redfield",
            schedule: "Senin 08:00 - 10:00 (Lab Komputer 2)",
            group: "Kelompok 1 (Kel. 1)",
            modules: [
                { id: 1, num: "01", title: "Dasar HTML & Struktur Web", date: "Diunggah pada: 08 Sep 2025", desc: "Mempelajari sintaks dasar HTML5, struktur tag dokumen web, form input element, dan pengelompokan konten secara semantik." },
                { id: 2, num: "02", title: "CSS Styling & Layouting Flexbox", date: "Diunggah pada: 15 Sep 2025", desc: "Mengatur gaya tampilan halaman web dengan CSS, pemodelan box model, pewarnaan, tipografi, serta penataan tata letak menggunakan Flexbox." },
                { id: 3, num: "03", title: "CSS Grid & Responsive Design", date: "Diunggah pada: 22 Sep 2025", desc: "Penerapan CSS Grid Layout untuk desain grid 2 dimensi kompleks, serta media queries untuk pembuatan layout yang responsif di berbagai perangkat." },
                { id: 4, num: "04", title: "JavaScript DOM Manipulation & Events", date: "Diunggah pada: 29 Sep 2025", desc: "Pengenalan pemrograman JavaScript sisi klien, penanganan event, penyeleksian elemen DOM, serta memanipulasi struktur HTML secara interaktif." },
                { id: 5, num: "05", title: "PHP Scripting Basics", date: "Diunggah pada: 06 Okt 2025", desc: "Pengenalan sintaks pemrograman PHP di sisi server, variabel, tipe data, logika percabangan, struktur perulangan, fungsi, dan array." },
                { id: 6, num: "06", title: "Form Handling & Database Connection", date: "Diunggah pada: 13 Okt 2025", desc: "Penanganan form request ($_GET dan $_POST), teknik validasi input, koneksi database via PDO MySQL, dan eksekusi query CRUD dasar." },
                { id: 7, num: "07", title: "Implementasi MVC Arsitektur di PHP", date: "Diunggah pada: 20 Okt 2025", desc: "Restrukturisasi kode aplikasi web PHP Native ke dalam pola arsitektur Model-View-Controller (MVC) untuk kemudahan manajemen kode proyek." }
            ]
        },
        database: {
            lecturer: "Dr. Jill Valentine",
            assistant: "Claire Redfield",
            schedule: "Rabu 10:00 - 12:00 (Lab Komputer 1)",
            group: "Kelompok 3 (Kel. 3)",
            modules: [
                { id: 1, num: "01", title: "Entity-Relationship Diagram (ERD)", date: "Diunggah pada: 10 Sep 2025", desc: "Pemodelan data secara konseptual, penentuan entitas, atribut kunci, relasi antar-entitas, dan deskripsi kardinalitas studi kasus nyata." },
                { id: 2, num: "02", title: "DDL & DML Dasar SQL", date: "Diunggah pada: 17 Sep 2025", desc: "Pembuatan database, tabel, batasan constraint (primary key, foreign key, unique) menggunakan DDL, serta operasi insert, update, delete dengan DML." },
                { id: 3, num: "03", title: "Querying & Join Table", date: "Diunggah pada: 24 Sep 2025", desc: "Pengambilan data spesifik dan kompleks dengan kombinasi perintah SELECT, klausa WHERE, GROUP BY, dan relasi multi-tabel via INNER/LEFT/RIGHT JOIN." },
                { id: 4, num: "04", title: "Stored Procedure & Triggers", date: "Diunggah pada: 01 Okt 2025", desc: "Pembuatan fungsi prosedural di dalam DBMS dan trigger pemicu otomatis untuk melakukan integritas sinkronisasi data antar-tabel." }
            ]
        },
        network: {
            lecturer: "Prof. Leon S. Kennedy",
            assistant: "Ada Wong",
            schedule: "Jumat 14:00 - 16:00 (Lab Jaringan)",
            group: "Kelompok 2 (Kel. 2)",
            modules: [
                { id: 1, num: "01", title: "IP Address Subnetting (CIDR)", date: "Diunggah pada: 12 Sep 2025", desc: "Penghitungan pembagian jaringan IPv4, penentuan subnet mask, network address, broadcast address, serta alokasi range host IP menggunakan CIDR." },
                { id: 2, num: "02", title: "Routing Dinamis RIPv2 & OSPF", date: "Diunggah pada: 19 Sep 2025", desc: "Simulasi perutean paket data dinamis di lingkungan multi-router menggunakan protokol RIPv2 dan OSPF di Cisco Packet Tracer." }
            ]
        }
    };

    // DOM Elements
    const classSelector = document.getElementById('classSelector');
    const lecturerName = document.getElementById('lecturerName');
    const assistantName = document.getElementById('assistantName');
    const classSchedule = document.getElementById('classSchedule');
    const groupName = document.getElementById('groupName');
    const modulsContainer = document.getElementById('modulsContainer');
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');

    // Handle Class Selection Change
    classSelector.addEventListener('change', function() {
        const selectedClass = this.value;
        const data = classData[selectedClass];
        
        // Update details
        lecturerName.textContent = data.lecturer;
        assistantName.textContent = data.assistant;
        classSchedule.textContent = data.schedule;
        groupName.textContent = data.group;

        // Render Modules
        renderModules(data.modules);
    });

    // Function to render modules list
    function renderModules(modules) {
        modulsContainer.innerHTML = '';
        
        modules.forEach(modul => {
            const cardHtml = `
                <article class="modul-card" id="modul-${modul.id}">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">${modul.num}</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">${modul.title}</h3>
                                <span class="modul-date">${modul.date}</span>
                            </div>
                        </div>
                    </header>
                    <div class="modul-card-body">
                        <p class="modul-description">${modul.desc}</p>
                        
                        <div class="download-actions-row">
                            <a href="#" class="modul-download-btn btn-materi" onclick="event.preventDefault(); showToast('Mengunduh materi: ${modul.title}...');">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Unduh Modul</span>
                            </a>
                            <a href="#" class="modul-download-btn btn-petunjuk" onclick="event.preventDefault(); showToast('Mengunduh petunjuk tugas: Modul ${modul.num}...');">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span>Unduh Petunjuk Tugas</span>
                            </a>
                        </div>
                    </div>
                </article>
            `;
            modulsContainer.innerHTML += cardHtml;
        });
    }

    // Toast Notification helper
    function showToast(message) {
        toastMessage.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg> ${message}`;
        toast.className = "toast show";
        setTimeout(() => {
            toast.className = toast.className.replace("show", "");
        }, 3000);
    }

    // Initial load
    renderModules(classData.web.modules);
</script>

</body>
</html>
