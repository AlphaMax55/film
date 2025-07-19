
<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Movie.php';
require_once '../classes/Category.php';
require_once '../classes/MovieAPI.php';

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$movieAPI = new MovieAPI($db);
$category = new Category($db);
$categories = $category->getAllCategories();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_slug = $_POST['category'] ?? '';
    $year = $_POST['year'] ?? '';
    $country = $_POST['country'] ?? '';
    $min_rating = (float)($_POST['min_rating'] ?? 0);
    $pages = (int)($_POST['pages'] ?? 3);
    
    try {
        $saved_count = $movieAPI->fetchAndSaveMovies($category_slug, $year, $country, $min_rating, $pages);
        $message = "$saved_count yeni film başarıyla eklendi!";
    } catch (Exception $e) {
        $error = "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Çek - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-cog"></i> Admin Panel
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home"></i> Siteye Dön
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Çıkış
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="admin-sidebar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="movies.php">
                                <i class="fas fa-film"></i> Filmler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="fetch_movies.php">
                                <i class="fas fa-download"></i> Film Çek
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="categories.php">
                                <i class="fas fa-tags"></i> Kategoriler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users"></i> Kullanıcılar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="p-4">
                    <h2>API'den Film Çek</h2>
                    <p class="text-muted">TMDB API'sinden otomatik film çekme işlemi</p>
                    
                    <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $message ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-filter"></i> Filtreleme Seçenekleri</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Kategori</label>
                                                    <select name="category" class="form-select">
                                                        <option value="">Tüm Kategoriler</option>
                                                        <?php foreach ($categories as $cat): ?>
                                                        <option value="<?= $cat['slug'] ?>"><?= $cat['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Yıl</label>
                                                    <select name="year" class="form-select">
                                                        <option value="">Tüm Yıllar</option>
                                                        <?php for ($y = 2024; $y >= 1990; $y--): ?>
                                                        <option value="<?= $y ?>"><?= $y ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Ülke</label>
                                                    <select name="country" class="form-select">
                                                        <option value="">Tüm Ülkeler</option>
                                                        <option value="US">Amerika</option>
                                                        <option value="TR">Türkiye</option>
                                                        <option value="KR">Kore</option>
                                                        <option value="IN">Hindistan</option>
                                                        <option value="FR">Fransa</option>
                                                        <option value="DE">Almanya</option>
                                                        <option value="IT">İtalya</option>
                                                        <option value="ES">İspanya</option>
                                                        <option value="GB">İngiltere</option>
                                                        <option value="JP">Japonya</option>
                                                        <option value="CN">Çin</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Minimum IMDB Puanı</label>
                                                    <select name="min_rating" class="form-select">
                                                        <option value="0">Hepsi</option>
                                                        <option value="6">6.0+</option>
                                                        <option value="7">7.0+</option>
                                                        <option value="8">8.0+</option>
                                                        <option value="9">9.0+</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Sayfa Sayısı (Her sayfa ~20 film)</label>
                                                    <select name="pages" class="form-select">
                                                        <option value="1">1 Sayfa (~20 film)</option>
                                                        <option value="2">2 Sayfa (~40 film)</option>
                                                        <option value="3" selected>3 Sayfa (~60 film)</option>
                                                        <option value="5">5 Sayfa (~100 film)</option>
                                                        <option value="10">10 Sayfa (~200 film)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-download"></i> Filmleri Çek
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle"></i> Bilgilendirme</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <strong>Önemli:</strong> Bu özelliği kullanmadan önce TMDB API anahtarınızı 
                                        <code>classes/MovieAPI.php</code> dosyasındaki <code>$tmdb_api_key</code> 
                                        değişkenine eklemeyi unutmayın.
                                    </div>
                                    
                                    <h6>Öne Çıkan Kategoriler:</h6>
                                    <div class="d-grid gap-2">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="category" value="aksiyon">
                                            <input type="hidden" name="min_rating" value="7">
                                            <input type="hidden" name="pages" value="2">
                                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                                Aksiyon Filmleri (7.0+)
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="year" value="2024">
                                            <input type="hidden" name="pages" value="3">
                                            <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                                2024 Filmleri
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="min_rating" value="8">
                                            <input type="hidden" name="pages" value="2">
                                            <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                                IMDB 8.0+ Filmler
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="country" value="TR">
                                            <input type="hidden" name="pages" value="2">
                                            <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                                Türk Filmleri
                                            </button>
                                        </form>
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
