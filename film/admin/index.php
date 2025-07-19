<?php
// Output buffering to prevent header errors
ob_start();
session_start();

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';
require_once '../classes/Movie.php';
require_once '../classes/User.php';
require_once '../classes/Comment.php';

$database = new Database();
$db = $database->getConnection();

$movie = new Movie($db);
$user = new User($db);
$comment = new Comment($db);

// İstatistikler
$total_movies = $movie->getTotalMovies();
$total_users = count($user->getAllUsers());
$pending_comments = $comment->getPendingCommentsCount();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CinemaMax</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-film"></i> CinemaMax
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?= $_SESSION['username'] ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home"></i> Ana Sayfa</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid" style="padding-top: 76px;">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <div class="admin-nav">
                    <a href="index.php" class="admin-nav-link active">
                        <i class="fas fa-dashboard"></i> Dashboard
                    </a>
                    <a href="movies.php" class="admin-nav-link">
                        <i class="fas fa-film"></i> Filmler
                    </a>
                    <a href="categories.php" class="admin-nav-link">
                        <i class="fas fa-list"></i> Kategoriler
                    </a>
                    <a href="users.php" class="admin-nav-link">
                        <i class="fas fa-users"></i> Kullanıcılar
                    </a>
                    <a href="comments.php" class="admin-nav-link">
                        <i class="fas fa-comments"></i> Yorumlar
                        <?php if ($pending_comments > 0): ?>
                        <span class="badge bg-danger ms-2"><?= $pending_comments ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="api-settings.php" class="admin-nav-link">
                        <i class="fas fa-cog"></i> API Ayarları
                    </a>
                    <a href="omdb_fetch.php" class="admin-nav-link">
                        <i class="fas fa-download"></i> OMDB Film Çek (API)
                    </a>
                    <a href="fetch_movies.php" class="admin-nav-link">
                        <i class="fas fa-download"></i> TMDB Film Çek (API)
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-dashboard"></i> Dashboard</h2>
                    <div class="text-muted">
                        <i class="fas fa-calendar"></i> <?= date('d.m.Y H:i') ?>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="h5 mb-0">Toplam Film</div>
                                        <div class="h3 mb-0"><?= $total_movies ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-film fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="movies.php" class="text-white">
                                    <small>Detayları Gör <i class="fas fa-angle-right"></i></small>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="h5 mb-0">Toplam Kullanıcı</div>
                                        <div class="h3 mb-0"><?= $total_users ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="users.php" class="text-white">
                                    <small>Detayları Gör <i class="fas fa-angle-right"></i></small>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="h5 mb-0">Bekleyen Yorum</div>
                                        <div class="h3 mb-0"><?= $pending_comments ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-comments fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="comments.php" class="text-white">
                                    <small>Detayları Gör <i class="fas fa-angle-right"></i></small>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="h5 mb-0">Site Ziyareti</div>
                                        <div class="h3 mb-0">-</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <small class="text-white">Yakında eklenecek</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-film"></i> Son Eklenen Filmler</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $recent_movies = $movie->getMovies(5, 0);
                                if (!empty($recent_movies)):
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-dark table-striped">
                                        <thead>
                                            <tr>
                                                <th>Film</th>
                                                <th>Kategori</th>
                                                <th>Puan</th>
                                                <th>Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recent_movies as $recent_movie): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= $recent_movie['poster_url'] ?>" width="40" height="60" class="me-2">
                                                    <?= $recent_movie['title'] ?>
                                                </td>
                                                <td><?= $recent_movie['category_name'] ?></td>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-star"></i> <?= $recent_movie['rating'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d.m.Y', strtotime($recent_movie['created_at'])) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">Henüz film eklenmemiş.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus"></i> Hızlı İşlemler</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="movies.php?action=add" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Yeni Film Ekle
                                    </a>
                                    <a href="categories.php?action=add" class="btn btn-success">
                                        <i class="fas fa-list"></i> Yeni Kategori Ekle
                                    </a>
                                    <a href="api-settings.php" class="btn btn-info">
                                        <i class="fas fa-download"></i> API'den Film Çek
                                    </a>
                                    <a href="comments.php" class="btn btn-warning">
                                        <i class="fas fa-comments"></i> Yorumları Yönet
                                    </a>
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
<?php
// Flush output buffer
ob_end_flush();
?>