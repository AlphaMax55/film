<?php
// Session'ı en başta başlat
session_start();

// Output buffering başlat
ob_start();
require_once 'config/database.php';
require_once 'classes/Movie.php';
require_once 'classes/Comment.php';
require_once 'classes/Rating.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$movie = new Movie($db);
$comment = new Comment($db);
$rating = new Rating($db);

$movie_id = $_GET['id'];
$movie_data = $movie->getMovieById($movie_id);

if (!$movie_data) {
    header('Location: index.php');
    exit;
}

// Yorumları çek
$comments = $comment->getMovieComments($movie_id);

// Kullanıcının puanını çek
$user_rating = null;
if (isset($_SESSION['user_id'])) {
    $user_rating = $rating->getUserRating($_SESSION['user_id'], $movie_id);
}

// Oyuncuları çek
$actors = $movie->getMovieActors($movie_id);

// Yorum gönderme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    if (isset($_SESSION['user_id'])) {
        $comment_text = trim($_POST['comment']);
        if (!empty($comment_text)) {
            $comment->addComment($_SESSION['user_id'], $movie_id, $comment_text);
            header('Location: movie.php?id=' . $movie_id);
            exit;
        }
    }
}

// Puan verme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rating'])) {
    if (isset($_SESSION['user_id'])) {
        $rating_value = (int)$_POST['rating'];
        if ($rating_value >= 1 && $rating_value <= 10) {
            $rating->addOrUpdateRating($_SESSION['user_id'], $movie_id, $rating_value);
            $movie->updateRating($movie_id);
            header('Location: movie.php?id=' . $movie_id);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $movie_data['title'] ?> - CinemaMax</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-film"></i> CinemaMax
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Ana Sayfa</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= $_SESSION['username'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($_SESSION['role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/index.php"><i class="fas fa-cog"></i> Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Giriş</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Kayıt</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Movie Detail Hero -->
    <section class="movie-detail-hero" style="background-image: url('<?= $movie_data['poster_url'] ?>')">
        <div class="movie-detail-overlay">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <img src="<?= $movie_data['poster_url'] ?>" alt="<?= $movie_data['title'] ?>" class="movie-poster-large">
                    </div>
                    <div class="col-lg-8">
                        <div class="movie-details">
                            <h1><?= $movie_data['title'] ?></h1>
                            <div class="movie-meta-large">
                                <span class="badge bg-warning">
                                    <i class="fas fa-star"></i> <?= round($movie_data['avg_rating'] ?? 0, 1) ?>
                                    (<?= $movie_data['rating_count'] ?? 0 ?> oy)
                                </span>
                                <span class="badge bg-info"><?= $movie_data['year'] ?></span>
                                <span class="badge bg-secondary"><?= $movie_data['duration'] ?> dk</span>
                                <span class="badge bg-primary"><?= $movie_data['category_name'] ?></span>
                            </div>
                            <p class="movie-description"><?= $movie_data['description'] ?></p>

                            <?php if (!empty($actors)): ?>
                            <div class="actors-section mb-3">
                                <h5>Oyuncular:</h5>
                                <div class="actors-list">
                                    <?php foreach($actors as $actor): ?>
                                    <span class="badge bg-dark me-2 mb-1"><?= $actor['name'] ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="movie-actions">
                                <button class="btn btn-primary btn-lg me-3" onclick="playMovie('<?= $movie_data['id'] ?>')">
                                    <i class="fas fa-play"></i> Filmi İzle
                                </button>
                                
                                <?php if (!empty($movie_data['movie_url'])): ?>
                                <button class="btn btn-success btn-lg" onclick="openMoviePlayer('<?= $movie_data['movie_url'] ?>')">
                                    <i class="fas fa-play"></i> Filmi İzle
                                </button>
                                <?php endif; ?>
                                
                                <?php if (!empty($movie_data['trailer_url'])): ?>
                                <a href="<?= $movie_data['trailer_url'] ?>" target="_blank" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-video"></i> Fragman
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Player Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white"><?= $movie_data['title'] ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="video-player">
                        <iframe id="moviePlayer" width="100%" height="500" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating and Comments Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Rating Section -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="col-lg-6">
                    <div class="comments-section">
                        <h4><i class="fas fa-star"></i> Film Puanla</h4>
                        <form method="POST">
                            <div class="rating-stars mb-3">
                                <?php for($i = 1; $i <= 10; $i++): ?>
                                <i class="fas fa-star <?= $user_rating && $user_rating['rating'] >= $i ? 'active' : '' ?>" 
                                   data-rating="<?= $i ?>" onclick="setRating(<?= $i ?>)"></i>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="<?= $user_rating['rating'] ?? 0 ?>">
                            <button type="submit" name="submit_rating" class="btn btn-primary">
                                <i class="fas fa-star"></i> Puanı Kaydet
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Comment Form -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="col-lg-6">
                    <div class="comments-section">
                        <h4><i class="fas fa-comment"></i> Yorum Yap</h4>
                        <form method="POST">
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="4" placeholder="Filmi nasıl buldunuz?" required></textarea>
                            </div>
                            <button type="submit" name="submit_comment" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Yorum Gönder
                            </button>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="col-12">
                    <div class="comments-section text-center">
                        <h4>Puan vermek ve yorum yapmak için <a href="login.php">giriş yapın</a></h4>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Comments List -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="comments-section">
                        <h4><i class="fas fa-comments"></i> Yorumlar (<?= count($comments) ?>)</h4>

                        <?php if (!empty($comments)): ?>
                            <?php foreach($comments as $comment_item): ?>
                            <div class="comment-item">
                                <div class="comment-author">
                                    <i class="fas fa-user-circle"></i> <?= $comment_item['username'] ?>
                                    <span class="comment-date"><?= date('d.m.Y H:i', strtotime($comment_item['created_at'])) ?></span>
                                </div>
                                <div class="comment-text"><?= nl2br(htmlspecialchars($comment_item['comment'])) ?></div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-film"></i> CinemaMax</h5>
                    <p>En iyi filmleri HD kalitesinde izleyin.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 CinemaMax. Tüm hakları saklıdır.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Movie Player Modal -->
    <div class="modal fade" id="moviePlayerModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Film İzle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe id="movieFrame" src="" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openMoviePlayer(movieUrl) {
            document.getElementById('movieFrame').src = movieUrl;
            new bootstrap.Modal(document.getElementById('moviePlayerModal')).show();
        }
        
        // Modal kapandığında video'yu durdur
        document.getElementById('moviePlayerModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('movieFrame').src = '';
        });
        
        function setRating(rating) {
            document.getElementById('ratingInput').value = rating;

            // Yıldızları güncelle
            const stars = document.querySelectorAll('.rating-stars i');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        function playMovie(movieId) {
            // Sample movie URLs for demo purposes
            const sampleMovies = {
                '1': 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                '2': 'https://www.youtube.com/embed/ScMzIvxBSi4',
                '3': 'https://www.youtube.com/embed/hFZFjoX2cGg',
                '4': 'https://www.youtube.com/embed/F2bk_9T482g',
                '5': 'https://www.youtube.com/embed/TcMBFSGVi1c',
                '6': 'https://www.youtube.com/embed/QH2-TGUlwu4'
            };
            
            const iframe = document.getElementById('moviePlayer');
            const movieUrl = sampleMovies[movieId] || 'https://www.youtube.com/embed/dQw4w9WgXcQ';
            
            iframe.src = movieUrl;
            
            const modal = new bootstrap.Modal(document.getElementById('videoModal'));
            modal.show();
        }

        // Modal kapandığında iframe'i temizle
        document.getElementById('videoModal')?.addEventListener('hidden.bs.modal', function () {
            const iframe = document.getElementById('moviePlayer');
            if (iframe) {
                iframe.src = '';
            }
        });
    </script>
</body>
</html>