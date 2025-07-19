<?php
include 'data.php';
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dizi & Film İzle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Dizi & Film İzleme Sitesi</h1>
    <div class="content-list">
        <?php foreach ($contents as $item): ?>
            <div class="content-item">
                <a href="detail.php?id=<?= $item['id'] ?>">
                    <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                    <h2><?= htmlspecialchars($item['title']) ?></h2>
                </a>
                <p><?= htmlspecialchars($item['description']) ?></p>
                <span class="type"><?= strtoupper($item['type']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
