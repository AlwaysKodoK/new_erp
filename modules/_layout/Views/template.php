<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $this->renderSection('title') ?: 'ERP SIMRS' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="icon" href="<?= base_url('img/logo_royal.png') ?>" type="image/png">
    
    <style>  
        :root {
            --bs-font-sans-serif: 'Poppins', sans-serif;
            --bs-body-font-family: 'Poppins', sans-serif;
            --font-white: white;
            --bg-dark: rgba(255, 255, 255, 0.04);
        } 
        body {
            font-family: var(--bs-body-font-family); 
            height: 100vh;
            overflow: hidden;
            background: #121212 !important;
        }
        
        .wrapper {
            width: 100%;
            height: 100vh;
        }
 
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #adb5bd; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6c757d; }


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
            color: var(--font-white) !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
        }

        .cardDark,
        .modalDark .modal-content {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important; 
            color: var(--font-white) !important;
        }

        .cardDark .form-control,
        .cardDark .form-select,
        .modalDark .form-control,
        .modalDark .form-select {
            background-color: var(--bg-dark) !important; 
            color: #e9ecef;
            border: 1px solid #444a54;
        }

        .cardDark .form-control:focus,
        .cardDark .form-select:focus,
        .modalDark .form-control:focus,
        .modalDark .form-select:focus {
            background-color: var(--bg-dark) !important;
            color: #e9ecef;
            border-color: #6c757d;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
        }

        .cardDark .form-select option,
        .modalDark .form-select option {
            background-color: #1a1d21 !important; 
            color: #e9ecef !important;
        }

        .cardDark .form-control::placeholder,
        .modalDark .form-control::placeholder {
            color: #8a8f98;
        }

        .cardDark .form-label,
        .modalDark .form-label {
            color: #cfd3d8;
        }

        .cardDark input[type="date"]::-webkit-calendar-picker-indicator,
        .modalDark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
    </style>
</head>
<body class="d-flex wrapper">

    <?= view_cell('App\Cells\SidebarCell::render') ?>
    
    <div class="main-content flex-grow-1 d-flex flex-column h-100" style="min-width: 0;"> 
        <?= view_cell('App\Cells\NavbarCell::render') ?>
        
        <main class="p-2 flex-grow-1 overflow-auto">
            <?= $this->renderSection('konten'); ?>
        </main>
    </div> 
 

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- TAMBAHKAN SECTION SCRIPT DI SINI -->
    <?= $this->renderSection('script') ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const sidebarToggleBtn = document.getElementById("sidebarToggle");
 
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltips = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
 
            tooltipTriggerList.forEach(el => {
                el.addEventListener('show.bs.tooltip', (e) => { 
                    if (!sidebar.classList.contains("collapsed")) {
                        e.preventDefault();
                    }
                });
            }); 
            if (sidebarToggleBtn && sidebar) {
                sidebarToggleBtn.addEventListener("click", function() {
                    sidebar.classList.toggle("collapsed"); 
                    if(sidebar.classList.contains('collapsed')) {
                        const openedMenus = sidebar.querySelectorAll('.collapse.show');
                        openedMenus.forEach(menu => {
                            let bsCollapse = bootstrap.Collapse.getInstance(menu);
                            if(bsCollapse) bsCollapse.hide();
                        });
                         
                        tooltips.forEach(t => t.hide());
                    }
                });
            }
        });

        $(document).on('click', '#formLogout button', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: "Anda akan mengakhiri sesi login saat ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>