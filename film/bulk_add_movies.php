
<?php
require_once 'config/database.php';
require_once 'classes/Movie.php';
require_once 'classes/Category.php';

set_time_limit(0); // Zaman limitini kaldır

$database = new Database();
$db = $database->getConnection();

$movie = new Movie($db);
$category = new Category($db);

// Kategorileri al
$categories = $category->getAllCategories();
$category_map = [];
foreach ($categories as $cat) {
    $category_map[$cat['slug']] = $cat['id'];
}

// Her kategori için film listesi
$movies_by_category = [
    'aksiyon' => [
        ['title' => 'John Wick', 'description' => 'Emekli suikastçının intikam hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/fZPSd91yGE9fCcCe6OoQr6E3Bev.jpg', 'year' => 2014, 'rating' => 7.4, 'duration' => 101],
        ['title' => 'Mad Max: Fury Road', 'description' => 'Post-apokaliptik aksiyon filmi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/hA2ple9q4qnwxp3hKVNhroipsir.jpg', 'year' => 2015, 'rating' => 8.1, 'duration' => 120],
        ['title' => 'The Dark Knight', 'description' => 'Batman ve Joker\'in epik mücadelesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 'year' => 2008, 'rating' => 9.0, 'duration' => 152],
        ['title' => 'Gladiator', 'description' => 'Roma\'da gladyatörün hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/ty8TGRuvJLPUmAR1H1nRIsgwvim.jpg', 'year' => 2000, 'rating' => 8.5, 'duration' => 155],
        ['title' => 'Die Hard', 'description' => 'Klasik aksiyon filmi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/yFihWxQcmqcaBR31QM6Y8gT6aYV.jpg', 'year' => 1988, 'rating' => 8.2, 'duration' => 132],
        ['title' => 'Terminator 2', 'description' => 'Gelecekten gelen makineler', 'poster_url' => 'https://image.tmdb.org/t/p/w500/5M0j0B18abtBI5gi2RhfjjurTqb.jpg', 'year' => 1991, 'rating' => 8.5, 'duration' => 137],
        ['title' => 'The Matrix', 'description' => 'Sanal gerçeklik aksiyon filmi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg', 'year' => 1999, 'rating' => 8.7, 'duration' => 136],
        ['title' => 'Casino Royale', 'description' => 'James Bond\'un yeni macerası', 'poster_url' => 'https://image.tmdb.org/t/p/w500/zlVMocZhNHVjMbqLGkr9gEkhdFi.jpg', 'year' => 2006, 'rating' => 8.0, 'duration' => 144],
        ['title' => 'Iron Man', 'description' => 'Marvel\'in süper kahramanı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/78lPtwv72eTNqFW9COBYI0dWDJa.jpg', 'year' => 2008, 'rating' => 7.9, 'duration' => 126],
        ['title' => 'Fast Five', 'description' => 'Hızlı ve Öfkeli serisi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/w4FPIZw7lUYSr4F4i2adPbqL7EI.jpg', 'year' => 2011, 'rating' => 7.3, 'duration' => 130]
    ],
    'komedi' => [
        ['title' => 'The Hangover', 'description' => 'Vegas\'ta unutulmaz gece', 'poster_url' => 'https://image.tmdb.org/t/p/w500/uluhlXqQpaBia1nUbsR2Q3BI0W5.jpg', 'year' => 2009, 'rating' => 7.7, 'duration' => 100],
        ['title' => 'Superbad', 'description' => 'Gençlik komedisi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/ek8e8txUyUwd2BNqj6lFEerJfbq.jpg', 'year' => 2007, 'rating' => 7.6, 'duration' => 113],
        ['title' => 'Dumb and Dumber', 'description' => 'Aptal ve daha aptal', 'poster_url' => 'https://image.tmdb.org/t/p/w500/4LdpBXiCyGKkR8FGOGLbOIUHeCz.jpg', 'year' => 1994, 'rating' => 7.3, 'duration' => 107],
        ['title' => 'Anchorman', 'description' => 'Haber spikeri komedisi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/7f9kgX6b9HBv4G4hHhQ1q6z3Igq.jpg', 'year' => 2004, 'rating' => 7.2, 'duration' => 94],
        ['title' => 'Zoolander', 'description' => 'Model dünyası komedisi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/qdrbGAr7BdYTr0qgWJm9GdY0Qm6.jpg', 'year' => 2001, 'rating' => 6.5, 'duration' => 90],
        ['title' => 'Meet the Parents', 'description' => 'Kayınpederle tanışma', 'poster_url' => 'https://image.tmdb.org/t/p/w500/mJw7tKRBGEo5YcVE9KJTQ7r8CTr.jpg', 'year' => 2000, 'rating' => 7.0, 'duration' => 108],
        ['title' => 'Borat', 'description' => 'Kazak gazeteci Amerika\'da', 'poster_url' => 'https://image.tmdb.org/t/p/w500/2Q7aJCCJOPOeB0BcETy0nHVlTa7.jpg', 'year' => 2006, 'rating' => 7.3, 'duration' => 84],
        ['title' => 'Step Brothers', 'description' => 'Üvey kardeşler', 'poster_url' => 'https://image.tmdb.org/t/p/w500/wRR62xjd9Nql8gh9fEpNaRoQh3i.jpg', 'year' => 2008, 'rating' => 6.9, 'duration' => 98],
        ['title' => 'Tropic Thunder', 'description' => 'Oyuncu komedisi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/9VT6F44PchgSE2wKg8Jn5vvAUKM.jpg', 'year' => 2008, 'rating' => 7.0, 'duration' => 107],
        ['title' => 'Ace Ventura', 'description' => 'Hayvan dedektifi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/pqiRuETmuSybfnVZ7qyeoHPNHoh.jpg', 'year' => 1994, 'rating' => 6.9, 'duration' => 86]
    ],
    'dram' => [
        ['title' => 'The Shawshank Redemption', 'description' => 'Hapishane dramı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg', 'year' => 1994, 'rating' => 9.3, 'duration' => 142],
        ['title' => 'Forrest Gump', 'description' => 'Bir adamın hayat hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/saHP97rTPS5eLmrLQEcANmKrsFl.jpg', 'year' => 1994, 'rating' => 8.8, 'duration' => 142],
        ['title' => 'The Godfather', 'description' => 'Mafya babasının hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsRolD1fZdja1.jpg', 'year' => 1972, 'rating' => 9.2, 'duration' => 175],
        ['title' => 'Pulp Fiction', 'description' => 'Tarantino klasiği', 'poster_url' => 'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg', 'year' => 1994, 'rating' => 8.9, 'duration' => 154],
        ['title' => 'Goodfellas', 'description' => 'Mafya hayatı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/aKuFiU82s5ISJpGZp7YkIr3kCUd.jpg', 'year' => 1990, 'rating' => 8.7, 'duration' => 146],
        ['title' => 'The Departed', 'description' => 'Polis ve mafya', 'poster_url' => 'https://image.tmdb.org/t/p/w500/nT97ifVT2J1yMQmeq20Qblg61T.jpg', 'year' => 2006, 'rating' => 8.5, 'duration' => 151],
        ['title' => 'Fight Club', 'description' => 'Dövüş kulübü', 'poster_url' => 'https://image.tmdb.org/t/p/w500/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg', 'year' => 1999, 'rating' => 8.8, 'duration' => 139],
        ['title' => 'Taxi Driver', 'description' => 'Taksi şoförünün dramı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/ekstpH614XzJan3trWiF3nWAp6t.jpg', 'year' => 1976, 'rating' => 8.2, 'duration' => 114],
        ['title' => 'Scarface', 'description' => 'Tony Montana\'nın hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/iQ5ztdjvteGeboxtmRdXEChJOHh.jpg', 'year' => 1983, 'rating' => 8.3, 'duration' => 170],
        ['title' => 'Casablanca', 'description' => 'Klasik Hollywood dramı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/5K7cOHoay2mZusSLezBOY0Qxh8a.jpg', 'year' => 1942, 'rating' => 8.5, 'duration' => 102]
    ],
    'korku' => [
        ['title' => 'The Exorcist', 'description' => 'Şeytan çıkarma filmi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/4ucLGBTzf6XZ3Hj3T3ehLkzMEWq.jpg', 'year' => 1973, 'rating' => 8.0, 'duration' => 122],
        ['title' => 'Halloween', 'description' => 'Michael Myers\'ın terörü', 'poster_url' => 'https://image.tmdb.org/t/p/w500/wijlZ3HaYMvlDTPqJoTCWKFkCPU.jpg', 'year' => 1978, 'rating' => 7.7, 'duration' => 91],
        ['title' => 'A Nightmare on Elm Street', 'description' => 'Freddy Krueger\'ın kabusu', 'poster_url' => 'https://image.tmdb.org/t/p/w500/3xJNBk0BKKhJhp6R8r1HaJ1CbCR.jpg', 'year' => 1984, 'rating' => 7.5, 'duration' => 101],
        ['title' => 'The Shining', 'description' => 'Kubrick\'in korku klasiği', 'poster_url' => 'https://image.tmdb.org/t/p/w500/9fgh3Ns1iRzlQNYuJyK0ARQZU7w.jpg', 'year' => 1980, 'rating' => 8.4, 'duration' => 146],
        ['title' => 'Psycho', 'description' => 'Hitchcock\'un korku başyapıtı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/yz4QVqPx3h1hD1DfqqQkCq3rmxW.jpg', 'year' => 1960, 'rating' => 8.5, 'duration' => 109],
        ['title' => 'Scream', 'description' => 'Modern korku klasiği', 'poster_url' => 'https://image.tmdb.org/t/p/w500/7MW23wNhN8jtEh6GgzSMOHKGjmb.jpg', 'year' => 1996, 'rating' => 7.3, 'duration' => 111],
        ['title' => 'The Texas Chain Saw Massacre', 'description' => 'Teksas katliamı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/wMdDGjVDLjtF7LmprG8o3MAtf5F.jpg', 'year' => 1974, 'rating' => 7.5, 'duration' => 84],
        ['title' => 'Poltergeist', 'description' => 'Hayalet filmi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/65x3GlYJVGQlOcXHqWDrcwHQf59.jpg', 'year' => 1982, 'rating' => 7.3, 'duration' => 114],
        ['title' => 'The Ring', 'description' => 'Lanetli video kaseti', 'poster_url' => 'https://image.tmdb.org/t/p/w500/kCVhZQJRBWWXG0cDVUzGxwk2LCf.jpg', 'year' => 2002, 'rating' => 7.1, 'duration' => 115],
        ['title' => 'Saw', 'description' => 'Jigsaw\'in oyunu', 'poster_url' => 'https://image.tmdb.org/t/p/w500/9psuWm5SPcy0xOQwGr8X1ntG5dR.jpg', 'year' => 2004, 'rating' => 7.6, 'duration' => 103]
    ],
    'romantik' => [
        ['title' => 'Titanic', 'description' => 'Gemide aşk hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg', 'year' => 1997, 'rating' => 7.8, 'duration' => 194],
        ['title' => 'The Notebook', 'description' => 'Zamansız aşk', 'poster_url' => 'https://image.tmdb.org/t/p/w500/qom1SZSENdmHFNZBXbtJAU0WTlC.jpg', 'year' => 2004, 'rating' => 7.8, 'duration' => 123],
        ['title' => 'Dirty Dancing', 'description' => 'Dans ve aşk', 'poster_url' => 'https://image.tmdb.org/t/p/w500/itKxbbNBTDrrBwjlJsyeMoTySVd.jpg', 'year' => 1987, 'rating' => 7.0, 'duration' => 100],
        ['title' => 'Ghost', 'description' => 'Hayalet aşkı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/3kcEGnYBHDeqmdYf8ZRbKdfmlUy.jpg', 'year' => 1990, 'rating' => 7.1, 'duration' => 127],
        ['title' => 'Pretty Woman', 'description' => 'Prens ve fahişe', 'poster_url' => 'https://image.tmdb.org/t/p/w500/1R6cvRtZgsYCkh8UFuWFN33xBP4.jpg', 'year' => 1990, 'rating' => 7.0, 'duration' => 119],
        ['title' => 'Sleepless in Seattle', 'description' => 'Radyo aşkı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/aFkf1xVCYXuvyKSHR6L0OhqItZp.jpg', 'year' => 1993, 'rating' => 6.8, 'duration' => 105],
        ['title' => 'When Harry Met Sally', 'description' => 'Arkadaşlık ve aşk', 'poster_url' => 'https://image.tmdb.org/t/p/w500/3wkbKeowUp1Zpkw1KkBqMWbt0P9.jpg', 'year' => 1989, 'rating' => 7.7, 'duration' => 96],
        ['title' => 'Love Actually', 'description' => 'Çoklu aşk hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/9OdW6MInE2jdvHTKNYjJX0mD2KF.jpg', 'year' => 2003, 'rating' => 7.6, 'duration' => 135],
        ['title' => 'The Princess Bride', 'description' => 'Masal aşkı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/njnm3vlKtGHVOhCE3Zq8UHbOGPf.jpg', 'year' => 1987, 'rating' => 8.0, 'duration' => 98],
        ['title' => 'Casablanca', 'description' => 'Savaş zamanı aşkı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/5K7cOHoay2mZusSLezBOY0Qxh8a.jpg', 'year' => 1942, 'rating' => 8.5, 'duration' => 102]
    ],
    'bilim-kurgu' => [
        ['title' => 'Star Wars', 'description' => 'Uzay destanı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/6FfCtAuVAW8XJjZ7eWeLibRLWTw.jpg', 'year' => 1977, 'rating' => 8.6, 'duration' => 121],
        ['title' => 'Blade Runner', 'description' => 'Android avcısı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/63N9uy8nd9j7Eog2axPQ8lbr3Wj.jpg', 'year' => 1982, 'rating' => 8.1, 'duration' => 117],
        ['title' => '2001: A Space Odyssey', 'description' => 'Uzay yolculuğu', 'poster_url' => 'https://image.tmdb.org/t/p/w500/ve72VxNqjGM69Uky4WTo2bK6rfq.jpg', 'year' => 1968, 'rating' => 8.3, 'duration' => 149],
        ['title' => 'E.T.', 'description' => 'Uzaylı dostluğu', 'poster_url' => 'https://image.tmdb.org/t/p/w500/tmuLPfhe9qgaeI9b4VU8rLSUa1O.jpg', 'year' => 1982, 'rating' => 7.8, 'duration' => 115],
        ['title' => 'Back to the Future', 'description' => 'Zaman yolculuğu', 'poster_url' => 'https://image.tmdb.org/t/p/w500/fNOH9f1aA7XRTzl1sAOx9iF553Q.jpg', 'year' => 1985, 'rating' => 8.5, 'duration' => 116],
        ['title' => 'Alien', 'description' => 'Uzayda korku', 'poster_url' => 'https://image.tmdb.org/t/p/w500/vfrQk5IPloGg1v9Rzbh2Eg3VGyM.jpg', 'year' => 1979, 'rating' => 8.4, 'duration' => 117],
        ['title' => 'The Thing', 'description' => 'Antarktika\'da uzaylı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/tzGY49kseSE9QAKk47uuDGwnSCu.jpg', 'year' => 1982, 'rating' => 8.1, 'duration' => 109],
        ['title' => 'Close Encounters', 'description' => 'Üçüncü tür yakınlaşma', 'poster_url' => 'https://image.tmdb.org/t/p/w500/yBEe4pJkP6gUWd5fhfJH3P47u7L.jpg', 'year' => 1977, 'rating' => 7.6, 'duration' => 138],
        ['title' => 'Total Recall', 'description' => 'Hafıza manipülasyonu', 'poster_url' => 'https://image.tmdb.org/t/p/w500/p9s2eEm9Y4sTqz2tMUGGFgvI5v9.jpg', 'year' => 1990, 'rating' => 7.5, 'duration' => 113],
        ['title' => 'District 9', 'description' => 'Uzaylı mülteciler', 'poster_url' => 'https://image.tmdb.org/t/p/w500/17ckMdce9aZGH6I6oCGWgb6Wfe0.jpg', 'year' => 2009, 'rating' => 7.9, 'duration' => 112]
    ],
    'fantastik' => [
        ['title' => 'The Lord of the Rings', 'description' => 'Orta Dünya destanı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/6oom5QYQ2yQTMJIbnvbkBL9cHo6.jpg', 'year' => 2001, 'rating' => 8.8, 'duration' => 178],
        ['title' => 'Harry Potter', 'description' => 'Büyücü çocuğun hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/wuMc08IPKEatf9rnMNXvIDxqP4W.jpg', 'year' => 2001, 'rating' => 7.6, 'duration' => 152],
        ['title' => 'Pan\'s Labyrinth', 'description' => 'Karanlık masal', 'poster_url' => 'https://image.tmdb.org/t/p/w500/8OjJINzJMdkVKmJ9lmOGRXlGNGH.jpg', 'year' => 2006, 'rating' => 8.2, 'duration' => 118],
        ['title' => 'The Chronicles of Narnia', 'description' => 'Narnia dünyası', 'poster_url' => 'https://image.tmdb.org/t/p/w500/uDMdcBhEWNUc5Z5nSdnrX7hZ5VX.jpg', 'year' => 2005, 'rating' => 6.9, 'duration' => 143],
        ['title' => 'The Princess Bride', 'description' => 'Fantastik macera', 'poster_url' => 'https://image.tmdb.org/t/p/w500/njnm3vlKtGHVOhCE3Zq8UHbOGPf.jpg', 'year' => 1987, 'rating' => 8.0, 'duration' => 98],
        ['title' => 'Big Fish', 'description' => 'Büyük balık hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/npuFayB6zPbF9MaExVdqQZLtF8k.jpg', 'year' => 2003, 'rating' => 8.0, 'duration' => 125],
        ['title' => 'Edward Scissorhands', 'description' => 'Makaslı eller', 'poster_url' => 'https://image.tmdb.org/t/p/w500/jFTLYTcJI0eZsB5yOdAXTDlhB6o.jpg', 'year' => 1990, 'rating' => 7.9, 'duration' => 105],
        ['title' => 'The Shape of Water', 'description' => 'Su canavarı aşkı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/iLYLADGA5oKGM92Ns1j8BxXzWEV.jpg', 'year' => 2017, 'rating' => 7.3, 'duration' => 123],
        ['title' => 'Pirates of the Caribbean', 'description' => 'Korsan maceraları', 'poster_url' => 'https://image.tmdb.org/t/p/w500/z8onk7LV9Mmw6zKz4hT6pzzvmvl.jpg', 'year' => 2003, 'rating' => 8.0, 'duration' => 143],
        ['title' => 'The NeverEnding Story', 'description' => 'Sonsuz hikaye', 'poster_url' => 'https://image.tmdb.org/t/p/w500/c0kKO8SqYwDY6DowITZh5PzSz8F.jpg', 'year' => 1984, 'rating' => 7.4, 'duration' => 102]
    ],
    'gerilim' => [
        ['title' => 'Se7en', 'description' => 'Yedi ölümcül günah', 'poster_url' => 'https://image.tmdb.org/t/p/w500/6yoghtyTpznpBik8EngEmJskVUO.jpg', 'year' => 1995, 'rating' => 8.6, 'duration' => 127],
        ['title' => 'Silence of the Lambs', 'description' => 'Hannibal Lecter\'in hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/rplLJ2hPcOQmkFhTqUte0MkEaO2.jpg', 'year' => 1991, 'rating' => 8.6, 'duration' => 118],
        ['title' => 'Zodiac', 'description' => 'Zodyak katili', 'poster_url' => 'https://image.tmdb.org/t/p/w500/6YmeO4pPbEliFckJkj4nb7p5EeY.jpg', 'year' => 2007, 'rating' => 7.7, 'duration' => 157],
        ['title' => 'The Sixth Sense', 'description' => 'Altıncı his', 'poster_url' => 'https://image.tmdb.org/t/p/w500/fIssD3w3SvIhPPmVo4WMgZDVLID.jpg', 'year' => 1999, 'rating' => 8.1, 'duration' => 107],
        ['title' => 'Shutter Island', 'description' => 'Gizemli ada', 'poster_url' => 'https://image.tmdb.org/t/p/w500/52d7CAFdaX1p8lwnVGpjjQZ9p53.jpg', 'year' => 2010, 'rating' => 8.2, 'duration' => 138],
        ['title' => 'Memento', 'description' => 'Hafıza kaybı gerilimi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/yuNs09hvpHVU1cBTCAk9zxsL2oW.jpg', 'year' => 2000, 'rating' => 8.4, 'duration' => 113],
        ['title' => 'Gone Girl', 'description' => 'Kayıp eş gerilimi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/r0cGjcKfwWcVo6zfCRk8rOu4jSW.jpg', 'year' => 2014, 'rating' => 8.1, 'duration' => 149],
        ['title' => 'Prisoners', 'description' => 'Kayıp çocuklar', 'poster_url' => 'https://image.tmdb.org/t/p/w500/jkKjF1B4bWRshWBJYXBdIL2ynPs.jpg', 'year' => 2013, 'rating' => 8.1, 'duration' => 153],
        ['title' => 'No Country for Old Men', 'description' => 'Modern western gerilim', 'poster_url' => 'https://image.tmdb.org/t/p/w500/bj1v6YKF8yHqA489VFfnQvUX5LJ.jpg', 'year' => 2007, 'rating' => 8.1, 'duration' => 122],
        ['title' => 'Heat', 'description' => 'Polis ve hırsız', 'poster_url' => 'https://image.tmdb.org/t/p/w500/zMyfPUelumio3tiDKPffaUpsQTD.jpg', 'year' => 1995, 'rating' => 8.2, 'duration' => 170]
    ],
    'macera' => [
        ['title' => 'Indiana Jones', 'description' => 'Arkeolog maceracı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/ceG9VzoRAVGwivFU403Wc3AHRys.jpg', 'year' => 1981, 'rating' => 8.5, 'duration' => 115],
        ['title' => 'The Goonies', 'description' => 'Çocukların macerası', 'poster_url' => 'https://image.tmdb.org/t/p/w500/eBU7gCjTCj9n2LTxvCSqAejZjVD.jpg', 'year' => 1985, 'rating' => 7.7, 'duration' => 114],
        ['title' => 'Jurassic Park', 'description' => 'Dinozor parkı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/b1xCNnyrPebIc7EWNZIa6BnJVxj.jpg', 'year' => 1993, 'rating' => 8.1, 'duration' => 127],
        ['title' => 'The Mummy', 'description' => 'Mumya maceraları', 'poster_url' => 'https://image.tmdb.org/t/p/w500/2jOjNhWQCiB19P1ZwWtOUFECpgC.jpg', 'year' => 1999, 'rating' => 7.0, 'duration' => 124],
        ['title' => 'National Treasure', 'description' => 'Hazine avcısı', 'poster_url' => 'https://image.tmdb.org/t/p/w500/luNqcs9de5srfKzthN0g4V6QAzP.jpg', 'year' => 2004, 'rating' => 6.9, 'duration' => 131],
        ['title' => 'The Adventures of Tintin', 'description' => 'Tenten\'in maceraları', 'poster_url' => 'https://image.tmdb.org/t/p/w500/qzDOTVnJJrNqeRvXXEqXhbPPVJn.jpg', 'year' => 2011, 'rating' => 7.3, 'duration' => 107],
        ['title' => 'Jungle Cruise', 'description' => 'Orman nehri macerasİ', 'poster_url' => 'https://image.tmdb.org/t/p/w500/9dKCd55IuTT5QRs989m9Qlb7d2B.jpg', 'year' => 2021, 'rating' => 6.6, 'duration' => 127],
        ['title' => 'Treasure Island', 'description' => 'Hazine adası', 'poster_url' => 'https://image.tmdb.org/t/p/w500/fGt2Rz9zMGuFVAHBOTXnqgQNfI0.jpg', 'year' => 1990, 'rating' => 7.1, 'duration' => 132],
        ['title' => 'Around the World in 80 Days', 'description' => '80 günde devriâlem', 'poster_url' => 'https://image.tmdb.org/t/p/w500/xeA9k8mGiGfAKVkxLu7eaTHJ8Ku.jpg', 'year' => 2004, 'rating' => 5.9, 'duration' => 120],
        ['title' => 'The Mask of Zorro', 'description' => 'Zorro\'nun maskesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/9rOQBb9V0czsYuZaFGaSXyHYrPU.jpg', 'year' => 1998, 'rating' => 6.8, 'duration' => 136]
    ],
    'animasyon' => [
        ['title' => 'Toy Story', 'description' => 'Oyuncakların hikayesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPrlKpxab.jpg', 'year' => 1995, 'rating' => 8.3, 'duration' => 81],
        ['title' => 'The Lion King', 'description' => 'Aslan kral', 'poster_url' => 'https://image.tmdb.org/t/p/w500/sKCr78MXSLixwmZ8DyJLrpMsd15.jpg', 'year' => 1994, 'rating' => 8.5, 'duration' => 88],
        ['title' => 'Finding Nemo', 'description' => 'Nemo\'yu bulma', 'poster_url' => 'https://image.tmdb.org/t/p/w500/eHuGQ10FUzK1mdOY69wF5pGgEf5.jpg', 'year' => 2003, 'rating' => 8.2, 'duration' => 100],
        ['title' => 'Shrek', 'description' => 'Yeşil dev', 'poster_url' => 'https://image.tmdb.org/t/p/w500/dyhaB0PAhb5QtNsSkJjWbN2zIRH.jpg', 'year' => 2001, 'rating' => 7.9, 'duration' => 90],
        ['title' => 'Frozen', 'description' => 'Donmuş krallık', 'poster_url' => 'https://image.tmdb.org/t/p/w500/kgwjIb2JDHRhNk13lmSxiClFjVk.jpg', 'year' => 2013, 'rating' => 7.4, 'duration' => 102],
        ['title' => 'The Incredibles', 'description' => 'Süper kahraman ailesi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/2LqaLgk4Z226KkgPJuiOQ58wvrm.jpg', 'year' => 2004, 'rating' => 8.0, 'duration' => 115],
        ['title' => 'WALL-E', 'description' => 'Çöpçü robot', 'poster_url' => 'https://image.tmdb.org/t/p/w500/hbhFnRzzg6ZDmm8YAmxBnQpQIPh.jpg', 'year' => 2008, 'rating' => 8.4, 'duration' => 98],
        ['title' => 'Up', 'description' => 'Balonla yolculuk', 'poster_url' => 'https://image.tmdb.org/t/p/w500/vpbaStTMt8qqXaEgnOR2EE4DNJk.jpg', 'year' => 2009, 'rating' => 8.2, 'duration' => 96],
        ['title' => 'Monsters, Inc.', 'description' => 'Canavar şirketi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/wdWqBMqHlNBJNjOyXiWFbOvNkEp.jpg', 'year' => 2001, 'rating' => 8.1, 'duration' => 92],
        ['title' => 'How to Train Your Dragon', 'description' => 'Ejder eğitimi', 'poster_url' => 'https://image.tmdb.org/t/p/w500/ygGmAO60t8GyqUo9xYeYxSZAR3b.jpg', 'year' => 2010, 'rating' => 8.1, 'duration' => 98]
    ]
];

echo "<h1>Toplu Film Ekleme İşlemi Başlıyor...</h1>\n";
echo "<style>body{font-family:Arial;margin:20px;}</style>\n";

$total_added = 0;

foreach ($movies_by_category as $category_slug => $movies) {
    if (!isset($category_map[$category_slug])) {
        echo "<p style='color:red;'>Kategori bulunamadı: $category_slug</p>\n";
        continue;
    }
    
    $category_id = $category_map[$category_slug];
    $added_count = 0;
    
    echo "<h2 style='color:#007bff;'>$category_slug kategorisine filmler ekleniyor...</h2>\n";
    
    foreach ($movies as $movie_data) {
        $movie_data['category_id'] = $category_id;
        $movie_data['featured'] = ($movie_data['rating'] >= 8.0) ? 1 : 0;
        $movie_data['status'] = 'active';
        $movie_data['trailer_url'] = '#';
        $movie_data['movie_url'] = '#';
        $movie_data['imdb_id'] = 'tt' . rand(1000000, 9999999);
        $movie_data['vote_count'] = rand(1000, 10000);
        $movie_data['backdrop_url'] = $movie_data['poster_url'];
        
        try {
            if ($movie->addMovie($movie_data)) {
                echo "<p style='color:green;'>✓ Film eklendi: " . $movie_data['title'] . "</p>\n";
                $added_count++;
                $total_added++;
            } else {
                echo "<p style='color:orange;'>⚠ Film zaten mevcut: " . $movie_data['title'] . "</p>\n";
            }
        } catch (Exception $e) {
            echo "<p style='color:red;'>✗ Hata: " . $movie_data['title'] . " - " . $e->getMessage() . "</p>\n";
        }
        
        // İşlemci yükünü azaltmak için kısa bekleme
        usleep(100000); // 0.1 saniye
        flush();
        ob_flush();
    }
    
    echo "<p style='color:#28a745;'><strong>$category_slug kategorisine $added_count film eklendi.</strong></p>\n";
    echo "<hr>\n";
}

echo "<h2 style='color:#28a745;'>Toplu film ekleme işlemi tamamlandı!</h2>\n";
echo "<p><strong>Toplam eklenen film sayısı: $total_added</strong></p>\n";
echo "<p><a href='index.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Ana Sayfaya Dön</a></p>\n";
?>
