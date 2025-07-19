
<?php
ob_start();
session_start();

// Zaten giriş yapmışsa ana sayfaya yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';
require_once 'classes/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        $user_data = $user->login($email, $password);
        
        if ($user_data) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['role'] = $user_data['role'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'Geçersiz email veya şifre!';
        }
    } else {
        $error = 'Tüm alanları doldurun!';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - CinemaMax</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <div class="col-lg-6 d-none d-lg-block" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1489599510673-95d76c68e6ff?w=1200') center/cover;">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center text-white">
                        <h1 class="display-4 mb-3"><i class="fas fa-film"></i> CinemaMax</h1>
                        <p class="lead">En iyi filmleri HD kalitesinde izleyin</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card bg-dark border-0">
                                <div class="card-body p-5">
                                    <h3 class="text-center mb-4 text-white">
                                        <i class="fas fa-sign-in-alt"></i> Giriş Yap
                                    </h3>
                                    
                                    <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="email" class="form-label text-white">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label text-white">Şifre</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100 mb-3">
                                            <i class="fas fa-sign-in-alt"></i> Giriş Yap
                                        </button>
                                    </form>
                                    
                                    <div class="text-center">
                                        <p class="text-muted">Hesabınız yok mu?</p>
                                        <a href="register.php" class="btn btn-outline-light">
                                            <i class="fas fa-user-plus"></i> Kayıt Ol
                                        </a>
                                    </div>
                                    
                                    <div class="text-center mt-3">
                                        <a href="index.php" class="text-muted">
                                            <i class="fas fa-arrow-left"></i> Ana Sayfaya Dön
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
