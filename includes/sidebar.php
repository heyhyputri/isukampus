<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Page Content Wrapper (Horizontal Header Navbar) -->
<div id="page-content-wrapper" class="w-100 d-flex flex-column">
    <!-- Horizontal Top Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-2.5 px-4">
        <div class="container-fluid p-0 d-flex align-items-center justify-content-between flex-wrap gap-3">
            
            <!-- Left: Logo & Branding -->
            <div class="d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3d281a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/>
                </svg>
                <span class="tracking-wide" style="font-weight: 600; font-size: 1.1rem; letter-spacing: 1px; color: #3d281a;">UKM PERS SUKMA</span>
            </div>
            
            <!-- Middle: Horizontal Navigation Menu -->
            <div class="d-flex align-items-center gap-3">
                <a href="index.php" class="nav-item-horiz text-decoration-none <?= $currentPage == 'index.php' || $currentPage == 'tambah.php' ? 'active-nav-horiz' : ''; ?>">
                    <i class="fa-regular fa-chart-bar me-2"></i>Dashboard
                </a>
                <?php if (isset($_SESSION['login'])) : ?>
                    <a href="kategori.php" class="nav-item-horiz text-decoration-none <?= $currentPage == 'kategori.php' ? 'active-nav-horiz' : ''; ?>">
                        <i class="fa-regular fa-bookmark me-2"></i>Kategori Isu
                    </a>
                    <a href="export.php" class="nav-item-horiz text-decoration-none <?= $currentPage == 'export.php' ? 'active-nav-horiz' : ''; ?>">
                        <i class="fa-regular fa-share-from-square me-2"></i>Ekspor Laporan
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Right: Date & Login/Profile -->
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted d-none d-xl-inline" style="font-size: 0.85rem; font-weight: 400;">
                    <?= date('d M Y'); ?>
                </span>
                
                <?php if (isset($_SESSION['login'])) : ?>
                    <span class="badge-online-minimal d-none d-sm-inline-flex">
                        Online
                    </span>
                    
                    <div class="vr d-none d-sm-block text-muted opacity-25" style="height: 20px;"></div>
                    
                    <div class="d-flex align-items-center">
                        <div class="user-avatar text-white me-2 d-flex align-items-center justify-content-center fw-bold" style="background-color: #6b7280; width: 32px; height: 32px; border-radius: 50%; font-size: 0.9rem;">
                            A
                        </div>
                        <div class="d-none d-md-block text-start me-2">
                            <p class="m-0 small fw-semibold text-dark" style="line-height: 1.2;">Administrator</p>
                            <p class="m-0 text-muted" style="font-size: 0.7rem; line-height: 1.2;">@admin</p>
                        </div>
                    </div>
                    
                    <a href="logout.php" class="btn btn-sm btn-danger-custom text-center px-3 py-1.5" style="border-radius: 4px; font-size: 0.8rem;" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1.5"></i>Logout
                    </a>
                <?php else : ?>
                    <a href="login.php" class="btn btn-sm btn-primary-custom text-center px-3 py-1.5" style="border-radius: 4px; font-size: 0.8rem; background-color: var(--primary-navy); border-color: var(--primary-navy);">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1.5"></i>Login Admin
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Container -->
    <div class="container-fluid px-4 py-4 flex-grow-1">
