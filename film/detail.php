<?php
include 'data.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$found = null;
foreach ($contents as $item) {
    if ($item['id'] === $id) {
        $found = $item;
        break;
    }
}
if (!$found) {
    echo "<h2>İçerik bulunamadı.</h2>";
    exit;
}
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($found['title']) ?> - İzle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a href="index.php">&lt; Geri Dön</a>
    <div class="detail-container">
        <img src="<?= $found['image'] ?>" alt="<?= htmlspecialchars($found['title']) ?>">
        <h1><?= htmlspecialchars($found['title']) ?></h1>
        <p><?= htmlspecialchars($found['description']) ?></p>
        <span class="type">Tür: <?= strtoupper($found['type']) ?></span>
        <h2>İzle</h2>
        <video width="640" height="360" controls>
            <source src="<?= $found['video'] ?>" type="video/mp4">
            Tarayıcınız video etiketini desteklemiyor.
        </video>
    </div>
</body>
</html>
