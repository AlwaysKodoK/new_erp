<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login' ?> | RS Royal Surabaya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?= base_url('img/logo_royal.png') ?>" type="image/png">
    <style>  
        :root {
            --bs-font-sans-serif: 'Poppins', sans-serif;
            --bs-body-font-family: 'Poppins', sans-serif;
        } 
        
        body {
            font-family: var(--bs-body-font-family); 
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(circle at 0% 0%, #3a3f47 0%, #151619 60%, #0d0e12 100%);
            color: #e0e0e0;
            position: relative;
        }

        /* Bungkus utama agar posisi absolut dialog Swal tidak mengganggu */
        .login-wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            padding: 15px;
            display: flex;
            justify-content: center;
        }
 
        .viewLogin {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.8);
            width: 100%;
            max-width: 500px;
        }

        .viewLogin p {
            color: #b0b4b8;
            line-height: 1.6;
            font-weight: 300;
            margin-bottom: 0;
        }

        /* --- GRADASI EMAS (LUXURY MINIMALIST GOLD) --- */
        .text-gold-gradient {
            background: linear-gradient(135deg, #C59B27 0%, #FFF2CD 40%, #D4AF37 80%, #9A7B1B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            letter-spacing: 0.5px;
        }
 
        .badge-luxury {
            background-color: rgba(255, 255, 255, 0.05);
            color: #d4af37 !important;
            border: 1px solid rgba(212, 175, 55, 0.3) !important;
            font-weight: 400;
        }

        /* --- FORM LUXURY STYLING --- */
        .login-body {
            width: 100%;
            margin-top: 30px;
        }

        .form-label-luxury {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #b0b4b8;
            margin-bottom: 0.4rem;
            display: block;
        }
 
        .form-control-luxury {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f7f7f7;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
 
        .form-control-luxury:focus {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: #D4AF37;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.15);
            color: #ffffff;
        }

        .form-control-luxury::placeholder {
            color: rgba(255, 255, 255, 0.4);
            font-weight: 300;
            opacity: 1;
        }

        .btn-gold {
            background: linear-gradient(135deg, #C59B27 0%, #D4AF37 50%, #9A7B1B 100%);
            border: none;
            color: #151619;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #FFF2CD 0%, #D4AF37 50%, #C59B27 100%);
            color: #000000;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
            transform: translateY(-2px);
        }

        /* ==== CUSTOM SWAL ====*/ 
        .swal2-popup {
            background: #151619 !important;
            color: #e0e0e0 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 16px !important;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.9) !important;
        }

        .swal2-title, .swal2-html-container {
            color: #e0e0e0 !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, #C59B27 0%, #D4AF37 50%, #9A7B1B 100%) !important;
            color: #151619 !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
        }
    </style>
</head>
<body> 
    
    <div class="login-wrapper">
        <section class="viewLogin"> 
            <div class="d-flex flex-column align-items-center mb-2">  
                <img src="<?= base_url('img/logo_royal.png') ?>" alt="Logo RS Royal Surabaya" class="img-fluid mb-3" style="max-width: 120px;"> 
                <h3 class="fw-semibold text-gold-gradient mb-2">RS Royal Surabaya</h3> 
                <span class="badge badge-luxury text-light fw-normal border text-wrap">Enterprise Resource Planning System</span>
            </div> 
            <div class="login-body">
                <form id="formLogin">
                    <div class="mb-3">
                        <label for="useridLogin" class="form-label-luxury">userid</label> 
                        <input type="text" class="form-control form-control-luxury" id="useridLogin" placeholder="Masukkan userid">
                    </div> 
                    
                    <div class="mb-4">
                        <label for="passwordLogin" class="form-label-luxury">Password</label> 
                        <div class="position-relative"> 
                            <input type="password" class="form-control form-control-luxury pe-5" id="passwordLogin" placeholder="Masukkan password"> 
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;">
                                <i class="fa-regular fa-eye-slash" id="iconPassword"></i>
                            </span>
                        </div>
                    </div> 
                    
                    <button type="button" class="btn btn-gold w-100" id="btnLogin" onclick="prosesLogin()">
                        Login
                    </button>
                </form>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() { 
            const togglePassword = document.querySelector("#togglePassword");
            const passwordLogin = document.querySelector("#passwordLogin");
            const iconPassword = document.querySelector("#iconPassword");

            togglePassword.addEventListener("click", function () { 
                const type = passwordLogin.getAttribute("type") === "password" ? "text" : "password";
                passwordLogin.setAttribute("type", type); 
                iconPassword.classList.toggle("fa-eye-slash");
                iconPassword.classList.toggle("fa-eye");
            });
        });

        function prosesLogin() {  
            var userid = $('#useridLogin').val().trim();
            var password = $('#passwordLogin').val().trim();
 
            if (!userid || !password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pastikan userid dan Password sudah terisi!'
                });
                return;
            }

            var btn = $('#btnLogin');
            var btnTextAsli = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Memproses...').prop('disabled', true);

            $.ajax({
                url: "<?= url_to('login.prosesLogin') ?>",
                type: "POST",
                dataType: "JSON", 
                data: {
                    userid: userid,
                    password: password
                },
                success: function(response) { 
                    btn.html(btnTextAsli).prop('disabled', false); 
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => { 
                            window.location.href = response.redirect || "<?= url_to('login.dashboardLogin') ?>"; 
                        });
                    } else { 
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) { 
                    btn.html(btnTextAsli).prop('disabled', false);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Terjadi kesalahan sistem saat menghubungi server.'
                    });
                }
            });
        }
    </script>
</body>
</html>