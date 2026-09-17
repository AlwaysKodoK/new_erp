<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> | RS Royal Surabaya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?= base_url('img/logo_royal.png') ?>" type="image/png">
    
    <style>  
        :root {
            --bs-font-sans-serif: 'Poppins', sans-serif;
            --bs-body-font-family: 'Poppins', sans-serif;
            --font-gold: #D4AF37;
            --font-white: #e0e0e0;
        } 
        
        body {
            font-family: var(--bs-body-font-family); 
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(circle at 0% 0%, #3a3f47 0%, #151619 60%, #0d0e12 100%);
            color: var(--font-white);
            padding: 40px 0;
        } 

        .viewDashboard {
            max-width: 1300px;
            margin: 0 auto;
        }

        .text-gold-gradient {
            background: linear-gradient(135deg, #C59B27 0%, #FFF2CD 40%, #D4AF37 80%, #9A7B1B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        /* ==== MENU CARD STYLING ==== */
        .menu-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px 32px; 
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            text-decoration: none;
            color: var(--font-white);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            background: rgba(212, 175, 55, 0.08);
            border-color: var(--font-gold);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.15);
            color: var(--font-gold);
        } 

        .menu-title {
            font-size: 1.5rem; 
            font-weight: 600; 
            margin: 0; 
            line-height: 1.4; 
        }

        /* ==== CUSTOM SWAL ==== */ 
        .swal2-popup {
            background: #151619 !important;
            color: var(--font-white) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 16px !important;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.9) !important;
        }

        .swal2-title, .swal2-html-container {
            color: var(--font-white) !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, #C59B27 0%, #D4AF37 50%, #9A7B1B 100%) !important;
            color: #151619 !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
        }
        
        .custom-search-input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important; 
            opacity: 1;
        }

        .custom-search-input:-ms-input-placeholder { 
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .custom-search-input::-ms-input-placeholder { 
            color: rgba(255, 255, 255, 0.6) !important;
        }
 
        .custom-search-input:focus {
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: var(--font-gold) !important; 
            color: #ffffff !important;
        }
    </style>
</head>
<body> 
    
    <div class="container-fluid px-4">
        <section class="viewDashboard">  
            <div class="text-center mb-5">
                <p class="text-uppercase tracking-wider text-muted mb-1" style="font-size: 0.85rem; letter-spacing: 2px;">
                    Sistem Informasi Manajemen RS
                </p>
                <h1 class="text-gold-gradient text-uppercase fw-bold display-6">
                    Selamat Datang, <br><?= esc($UserName); ?>
                </h1>
                <p class="mt-1">Pilih layanan/fitur yang ingin Anda akses di bawah ini</p>
                 
                <div class="row justify-content-center mt-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-secondary text-light">
                                <i class="fa-solid fa-search"></i>
                            </span>
                            <input type="text" id="searchMenu" class="custom-search-input form-control bg-transparent border-secondary text-light shadow-none" placeholder="Cari menu atau layanan...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-md-4" id="menuContainer">
                <?php if (!empty($DataMenu) && is_array($DataMenu)): ?>
                    <?php foreach ($DataMenu as $menu): ?>
                        <div class="col-12 col-sm-6 col-md-2 col-lg-2 menu-item-wrapper" data-name="<?= strtolower(esc($menu->MENU_NAMA)); ?>">
                            <div class="menu-card" onclick="open_dashboard('<?= esc($menu->MENU_URL); ?>')">
                                <h2 class="menu-title"><?= esc($menu->MENU_NAMA); ?></h2>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid fa-lock display-4 mb-3"></i>
                        <p class="">Anda tidak memiliki akses menu saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>
 
            <div id="noMenuFound" class="text-center py-5 d-none">
                <i class="fa-solid fa-face-frown display-4 mb-3"></i>
                <p class="">Menu yang Anda cari tidak ditemukan.</p>
            </div>

        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script> 
        document.getElementById('searchMenu').addEventListener('keyup', function() {
            let keyword = this.value.toLowerCase().trim();
            let menuItems = document.querySelectorAll('.menu-item-wrapper');
            let foundCount = 0;
            let noMenuFound = document.getElementById('noMenuFound');

            menuItems.forEach(function(item) {
                let menuName = item.getAttribute('data-name'); 
                if (menuName.includes(keyword)) {
                    item.style.display = 'block'; 
                    foundCount++;
                } else {
                    item.style.display = 'none'; 
                }
            });
 
            if (foundCount === 0) {
                noMenuFound.classList.remove('d-none');
            } else {
                noMenuFound.classList.add('d-none');
            }
        });

        function open_dashboard(MenuUrl) { 
            Swal.fire({
                title: 'Membuka Fitur',
                icon: 'success',
                timer: 1000,
                showConfirmButton: false
            }).then(() => {  
                let targetUrl = "<?= base_url(); ?>" + MenuUrl;  
                window.location.href = targetUrl;
            }); 
        }
    </script>
</body>
</html>