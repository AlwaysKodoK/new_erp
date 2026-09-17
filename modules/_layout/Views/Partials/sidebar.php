<style>
    .sidebar {
        width: 260px;
        height: 95vh;
        background: transparent; 
        z-index: 1040;
        transition: width 0.3s ease;
    }

    .sidebar-menu-container {
        position: relative; 
        z-index: 1045; 
        overflow-x: hidden;
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .sidebar .logo-full { display: block; }
    .sidebar .logo-compact { display: none; }
    
    .sidebar.collapsed { 
        width: 75px; 
        overflow: visible !important; 
    }
    .sidebar.collapsed .logo-full { display: none; }
    .sidebar.collapsed .logo-compact { display: block; }
    
    .sidebar.collapsed .menu-text,
    .sidebar.collapsed .chevron-icon {
        display: none !important;
    }
    
    .sidebar.collapsed .nav-link:not(.submenu-link) {
        justify-content: center !important;
        padding-left: 0;
        padding-right: 0;
    }
    
    .sidebar.collapsed .icon-w {
        margin-right: 0 !important;
        font-size: 1.25rem;
    }

    .sidebar.collapsed .sidebar-menu-container {
        overflow: visible !important;
    }
    
    .sidebar.collapsed .has-submenu {
        position: relative;
    }
 
    .sidebar.collapsed .has-submenu .collapse,
    .sidebar.collapsed .has-submenu .collapsing,
    .sidebar.collapsed .has-submenu .collapse.show {
        display: none !important; 
        position: absolute !important;
        left: 75px !important;
        top: 0 !important;
        width: 220px;
        background-color: #1a1d20;
        box-shadow: 4px 4px 16px rgba(0,0,0,0.4);
        border-radius: 0 0.5rem 0.5rem 0;
        border-left: 1px solid rgba(255, 255, 255, 0.08); 
        padding: 0.5rem;
        z-index: 1055;
        height: auto !important; 
        transition: none !important; 
        overflow: visible !important; 
    }

    .sidebar.collapsed .has-submenu .collapse::before,
    .sidebar.collapsed .has-submenu .collapsing::before,
    .sidebar.collapsed .has-submenu .collapse.show::before {
        content: '';
        position: absolute;
        left: -30px; 
        top: 0;
        width: 30px;
        height: 100%;
        background-color: transparent; 
    }

    .sidebar.collapsed .has-submenu:hover .collapse,
    .sidebar.collapsed .has-submenu:hover .collapsing,
    .sidebar.collapsed .has-submenu:hover .collapse.show {
        display: block !important;
    }

    .sidebar .flyout-header { display: none; }
    .sidebar.collapsed .flyout-header {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #9aa0a8;
        padding: 0.4rem 0.8rem;
        margin-bottom: 0.4rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .sidebar.collapsed .has-submenu .nav {
        margin: 0 !important;
        padding: 0 !important;
        border-left: none !important;
    }
    
    .sidebar.collapsed .submenu-link {
        justify-content: flex-start !important; 
        padding: 0.6rem 0.8rem !important;
        border-radius: 0.4rem;
    }
    .sidebar.collapsed .submenu-link:hover {
        background-color: #eeb61d !important;
        color: #1a1d20 !important;
        font-weight: 500;
    }
    
    .sidebar-brand {
        position: relative;
        z-index: 1045;
        height: 70px;
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        white-space: nowrap;
    }

    .sidebar .nav-link {
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
        color: #e4e6eb;
        font-weight: 500;
        padding: 0.75rem 1rem;
        margin-bottom: 0.2rem;
        white-space: nowrap;
    }
    
    .sidebar .nav-link:hover, 
    .sidebar .nav-link.active,
    .sidebar .nav-link[aria-expanded="true"] {
        background-color: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }
    
    .sidebar .nav-link:hover i.menu-icon,
    .sidebar .nav-link[aria-expanded="true"] i.menu-icon {
        color: #eeb61d !important;
    }

    .sidebar .chevron-icon {
        transition: transform 0.3s ease;
        font-size: 0.75rem;
    }
    .sidebar .nav-link[aria-expanded="true"] .chevron-icon {
        transform: rotate(180deg);
    }

    .sidebar .submenu-link {
        font-size: 0.85rem;
        font-weight: 400;
        color: #9aa0a8;
        transition: all 0.2s ease;
        border-radius: 0.4rem; 
    }
    
    .sidebar .submenu-link:hover {
        background-color: #eeb61d !important;
        color: #1a1d20 !important;
        font-weight: 500;
    }
    
    .icon-w {
        width: 24px;
        text-align: center;
    }
</style>

<div class="p-2">
    <aside id="sidebar" class="sidebar collapsed d-flex flex-column flex-shrink-0 ">
         
        <div class="sidebar-brand d-flex align-items-center justify-content-center rounded-3 mb-2"> 
            <a href="/" class="text-decoration-none">
                <img src="/img/logo_full.png" alt="Logo RS" class="logo-full" style="max-height: 45px; width: auto;">
                <img src="/img/logo_royal.png" alt="Logo Icon" class="logo-compact" style="max-height: 40px; width: auto;">
            </a>
        </div>
    
         <div class="sidebar-menu-container flex-grow-1 p-3 rounded-3">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item mt-1">
                    <a href="<?= site_url('login/dashboard'); ?>" class="nav-link d-flex align-items-center text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Dashboard Home"> 
                        <i class="fa-solid fa-home icon-w me-2 menu-icon"></i>
                        <span class="menu-text">Dashboard Home</span>
                    </a>
                </li>
                    
                <?php if (!empty($menus) && is_array($menus)): ?>
                    <?php foreach ($menus as $parent): ?> 
                        <?php if (empty($parent['children'])): ?>
                            <li class="nav-item mt-1">
                                <a href="<?= base_url($parent['MENU_URL'] ?? '#') ?>" class="nav-link d-flex align-items-center text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="<?= esc($parent['MENU_NAMA']) ?>">
                                    <!-- PERBAIKAN FORMAT TAG IKON -->
                                    <i class="<?= esc($parent['MENU_ICON'] ?? 'fa-solid fa-circle') ?> icon-w me-2 menu-icon"></i>
                                    <span class="menu-text"><?= esc($parent['MENU_NAMA']) ?></span>
                                </a>
                            </li>
    
                        <?php else: ?>
                            <?php $slug = url_title(strtolower($parent['MENU_NAMA']), '-', true); ?>
                            
                            <li class="nav-item mt-1 has-submenu text-uppercase">
                                <a href="#menu-<?= $slug ?>" data-bs-toggle="collapse" aria-expanded="false" class="nav-link d-flex align-items-center justify-content-between text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="<?= esc($parent['MENU_ICON'] ?? 'fa-solid fa-folder') ?> icon-w me-2 menu-icon"></i>
                                        <span class="menu-text"><?= esc($parent['MENU_NAMA']) ?></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down chevron-icon"></i>
                                </a>
                                
                                <div class="collapse" id="menu-<?= $slug ?>">
                                    <div class="flyout-header"><?= esc($parent['MENU_NAMA']) ?></div>
                                    <ul class="nav flex-column ms-4 mt-1 border-start border-2 border-secondary ps-1">
                                    
                                    <?php foreach ($parent['children'] as $child): ?>
                                        <?php 
                                            $urlPath = !empty($child['MENU_URL']) ? (route_to($child['MENU_URL']) ?: $child['MENU_URL']) : '#';
                                        ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url($urlPath) ?>" class="nav-link submenu-link py-1 px-2 d-flex align-items-center">
                                                <i class="<?= esc($child['MENU_ICON'] ?? 'fa-solid fa-circle-dot') ?> me-2" style="font-size: 6px;"></i>
                                                <span class="text-capitalize"><?= esc($child['MENU_NAMA']) ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
    
                                    </ul>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
    
            </ul>
        </div>
    </aside>
</div>