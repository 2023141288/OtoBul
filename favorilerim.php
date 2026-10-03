<?php
session_start();
require_once "baglanti.php";

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

$sorgu = $baglanti->prepare(
    "SELECT i.*,
        (
            SELECT f.dosya_yolu
            FROM ilan_fotograflari f
            WHERE f.ilan_id = i.id
            ORDER BY f.kapak_mi DESC, f.id ASC
            LIMIT 1
        ) AS kapak_fotografi
     FROM favoriler fav
     INNER JOIN ilanlar i ON i.id = fav.ilan_id
     WHERE fav.kullanici_id = :kullanici_id
     ORDER BY fav.id DESC"
);

$sorgu->execute([
    ":kullanici_id" => $_SESSION["kullanici_id"]
]);

$favoriIlanlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favori İlanlarım | OtoBul</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
        }

        header {
            background-color: #1769aa;
            padding: 16px 8%;
        }

        .logo {
            display: inline-block;
            background-color: white;
            color: #1769aa;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
        }

        h1 {
            color: #123b66;
            margin: 30px;
        }

        .ilan-listesi {
            display: grid;
            grid-template-columns: repeat(auto-fill, 380px);
            gap: 25px;
            padding: 0 30px 30px;
        }

        .ilan-karti {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            padding-bottom: 20px;
        }

        .ilan-karti img {
            width: 100%;
            height: 250px;
            object-fit: contain;
            background-color: #f1f1f1;
        }

        .ilan-karti h2,
        .ilan-karti p,
        .ilan-karti strong,
        .ilan-karti a {
            margin-left: 15px;
        }
    </style>
</head>

<body>

<header>
    <a class="logo" href="panel.php">OtoBul</a>
</header>

<h1>❤️ Favori İlanlarım</h1>

<div class="ilan-listesi">

    <?php foreach ($favoriIlanlar as $ilan): ?>

        <div class="ilan-karti">

            <?php if (!empty($ilan["kapak_fotografi"])): ?>
                <img
                    src="<?= htmlspecialchars($ilan["kapak_fotografi"]) ?>"
                    alt="Araç fotoğrafı"
                >
            <?php endif; ?>

            <h2><?= htmlspecialchars($ilan["baslik"]) ?></h2>

            <p>
                <?= htmlspecialchars($ilan["marka"]) ?>
                <?= htmlspecialchars($ilan["model"]) ?>
            </p>

            <p>
                <?= (int) $ilan["yil"] ?> |
                <?= number_format($ilan["kilometre"], 0, ",", ".") ?> km
            </p>

            <strong>
                <?= number_format($ilan["fiyat"], 0, ",", ".") ?> TL
            </strong>

            <br><br>

            <a href="ilan-detay.php?id=<?= (int) $ilan["id"] ?>">
                İlanı İncele
            </a>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>