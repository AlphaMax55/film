
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
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            if (strlen($password) >= 6) {
                if ($user->register($username, $email, $password)) {
                    $success = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
                } else {
                    $error = 'Bu email veya kullanıcı adı zaten kullanılıyor!';
                }
            } else {
                $error = 'Şifre en az 6 karakter olmalıdır!';
            }
        } else {
            $error = 'Şifreler eşleşmiyor!';
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
    <title>Kayıt Ol - CinemaMax</title>
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
                                        <i class="fas fa-user-plus"></i> Kayıt Ol
                                    </h3>
                                    
                                    <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($success)): ?>
                                    <div class="alert alert-success" role="alert">
                                        <i class="fas fa-check-circle"></i> <?= $success ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="username" class="form-label text-white">Kullanıcı Adı</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?= $_POST['username'] ?? '' ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label text-white">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= $_POST['email'] ?? '' ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label text-white">Şifre</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <div class="form-text text-muted">En az 6 karakter olmalıdır.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label text-white">Şifre Tekrar</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100 mb-3">
                                            <i class="fas fa-user-plus"></i> Kayıt Ol
                                        </button>
                                    </form>
                                    
                                    <div class="text-center">
                                        <p class="text-muted">Zaten hesabınız var mı?</p>
                                        <a href="login.php" class="btn btn-outline-light">
                                            <i class="fas fa-sign-in-alt"></i> Giriş Yap
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
