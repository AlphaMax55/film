
<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Movie.php';
require_once '../classes/Category.php';
require_once '../classes/OMDBMovieAPI.php';

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$omdbAPI = new OMDBMovieAPI($db);
$category = new Category($db);
$categories = $category->getAllCategories();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['fetch_popular'])) {
        try {
            $saved_count = $omdbAPI->fetchPopularMovies($categories, 2);
            $message = "$saved_count popüler film başarıyla eklendi!";
        } catch (Exception $e) {
            $error = "Hata: " . $e->getMessage();
        }
    } elseif (isset($_POST['fetch_by_category'])) {
        $category_slug = $_POST['category_slug'];
        $count = (int)($_POST['count'] ?? 20);
        
        try {
            $saved_count = $omdbAPI->fetchMoviesByCategory($category_slug, $categories, $count);
            $message = "$saved_count film '$category_slug' kategorisine başarıyla eklendi!";
        } catch (Exception $e) {
            $error = "Hata: " . $e->getMessage();
        }
    } elseif (isset($_POST['search_movies'])) {
        $search_term = $_POST['search_term'];
        $target_category = $_POST['target_category'];
        $pages = (int)($_POST['pages'] ?? 1);
        
        try {
            $saved_count = 0;
            for ($page = 1; $page <= $pages; $page++) {
                $movies = $omdbAPI->searchMovies($search_term, 'movie', '', $page);
                
                foreach ($movies as $movie) {
                    $details = $omdbAPI->getMovieDetails($movie['imdbID']);
                    if ($details) {
                        $movie_data = $omdbAPI->convertOMDBToMovie($details, $target_category);
                        if ($omdbAPI->saveMovieToDatabase($movie_data)) {
                            $saved_count++;
                        }
                    }
                    usleep(200000);
                }
                sleep(1);
            }
            $message = "$saved_count film '$search_term' aramasından başarıyla eklendi!";
        } catch (Exception $e) {
            $error = "Hata: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMDB Film Çek - Admin Panel</title>
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
                            <a class="nav-link active" href="omdb_fetch.php">
                                <i class="fas fa-download"></i> OMDB Film Çek
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="fetch_movies.php">
                                <i class="fas fa-film"></i> TMDB Film Çek
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="p-4">
                    <h2><i class="fas fa-database"></i> OMDB API'den Film Çek</h2>
                    <p class="text-muted">OMDB API'sinden otomatik film çekme işlemi (API Key: 5b86fb8b)</p>
                    
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
                        <!-- Popüler Filmler -->
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5><i class="fas fa-star"></i> Popüler Filmler</h5>
                                </div>
                                <div class="card-body">
                                    <p>Popüler filmleri otomatik olarak çeker ve kategorilere göre dağıtır.</p>
                                    <form method="POST">
                                        <button type="submit" name="fetch_popular" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-download"></i> Popüler Filmleri Çek (~50-100 film)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kategori Bazında Çekme -->
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5><i class="fas fa-tags"></i> Kategori Bazında</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Kategori Seç</label>
                                            <select name="category_slug" class="form-select" required>
                                                <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['slug'] ?>"><?= $cat['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Film Sayısı</label>
                                            <select name="count" class="form-select">
                                                <option value="10">10 Film</option>
                                                <option value="20" selected>20 Film</option>
                                                <option value="30">30 Film</option>
                                                <option value="50">50 Film</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="fetch_by_category" class="btn btn-success w-100">
                                            <i class="fas fa-download"></i> Bu Kategoriye Film Çek
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Arama ile Çekme -->
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5><i class="fas fa-search"></i> Arama ile Çek</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Arama Terimi</label>
                                            <input type="text" name="search_term" class="form-control" placeholder="batman, star wars, etc." required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hedef Kategori</label>
                                            <select name="target_category" class="form-select">
                                                <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sayfa Sayısı</label>
                                            <select name="pages" class="form-select">
                                                <option value="1" selected>1 Sayfa (~10 film)</option>
                                                <option value="2">2 Sayfa (~20 film)</option>
                                                <option value="3">3 Sayfa (~30 film)</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="search_movies" class="btn btn-info w-100">
                                            <i class="fas fa-search"></i> Ara ve Çek
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hızlı Kategoriler -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-bolt"></i> Hızlı Kategori Doldurma</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
                                        <div class="col-md-3 mb-2">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="category_slug" value="<?= $cat['slug'] ?>">
                                                <input type="hidden" name="count" value="15">
                                                <button type="submit" name="fetch_by_category" class="btn btn-outline-primary btn-sm w-100">
                                                    <?= $cat['name'] ?> (15 film)
                                                </button>
                                            </form>
                                        </div>
                                        <?php endforeach; ?>
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
