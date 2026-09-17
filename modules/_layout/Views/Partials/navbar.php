<style>
    .top-navbar {
        position: relative;
        z-index: 1045;
        height: 70px;
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .dropdown-toggle::after { display: none; }
    
    .user-profile-btn {
        padding: 0.4rem 0.8rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    
    .user-profile-btn:hover { background-color: rgba(255, 255, 255, 0.08); }
    
    #sidebarToggle {
        transition: color 0.2s;
    }
    #sidebarToggle:hover {
        color: #eeb61d !important;
    }

    .text-gold {
        color: #eeb61d !important;
    }

    /* === Fix: dropdown dipaksa jadi layer render terpisah, tidak ikut ke-blur === */
    .top-navbar .dropdown {
        position: relative;
        z-index: 1050;
    }

    .dropdown-menu-dark {
        isolation: isolate;
        -webkit-backdrop-filter: none !important;
        backdrop-filter: none !important;
        background-color: #1a1d20 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        z-index: 1060;
    }

    .dropdown-menu-dark .dropdown-header {
        color: #9aa0a8;
    }

    .dropdown-menu-dark .dropdown-item {
        color: #e4e6eb;
    }

    .dropdown-menu-dark .dropdown-item:not(.text-danger):hover {
        background-color: rgba(255, 255, 255, 0.08);
        color: #eeb61d !important;
    }

    .dropdown-menu-dark .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
</style>
<div class="p-2">
    <nav class="navbar top-navbar d-flex justify-content-between align-items-center px-4 rounded-3">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebarToggle" class="btn text-white p-0 border-0 fs-4 shadow-none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h5 class="mb-0 fw-bold text-white tracking-wide d-none d-sm-block">RS Royal Surabaya</h5>
        </div>
         
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle user-profile-btn" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="d-flex flex-column d-none d-md-flex me-3 text-end">
                    <!-- Data dinamis dari Cell -->
                    <span class="fw-semibold" style="font-size: 0.85rem;"><?= esc($displayName); ?></span>
                    <span class="" style="font-size: 0.75rem;"><?= esc($userLevel); ?></span>
                </div> 
                <!-- Avatar dinamis dari Cell -->
                <img src="<?= esc($avatarUrl); ?>" alt="User" class="rounded-circle border border-2 border-secondary" width="40" height="40">
            </a>
            
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUser">
                <li><h6 class="dropdown-header">Akun Saya</h6></li> 
                <li><a class="dropdown-item py-2" href="#"><i class="fa-regular fa-id-badge me-2 text-gold"></i> Profil Pengguna</a></li> 
                <li><hr class="dropdown-divider border-secondary"></li>
                <li>
                    <!-- Form Logout POST yang rapi dan aman -->
                    <form action="<?= site_url('login/Logout'); ?>" method="POST" id="formLogout" class="d-inline">
                        <?= csrf_field(); ?>
                        <button type="submit" class="dropdown-item text-danger py-2 fw-medium border-0 bg-transparent w-100 text-start" style="cursor: pointer;">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</div>