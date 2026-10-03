<?php
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı ilan detayını göremez.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Adresten gelen ilan numarasını al.
$ilan_id = (int) ($_GET["id"] ?? 0);

if ($ilan_id <= 0) {
    exit("Geçersiz ilan.");
}

// İlan bilgilerini getir.
$sorgu = $baglanti->prepare(
    "SELECT *
     FROM ilanlar
     WHERE id = :id"
);

$sorgu->execute([
    ":id" => $ilan_id
]);

$ilan = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$ilan) {
    exit("İlan bulunamadı.");
}
$favoriKontrol = $baglanti->prepare(
    "SELECT id
     FROM favoriler
     WHERE kullanici_id = :kullanici_id
     AND ilan_id = :ilan_id"
);

$favoriKontrol->execute([
    ":kullanici_id" => $_SESSION["kullanici_id"],
    ":ilan_id" => $ilan_id
]);

$favorideMi = $favoriKontrol->fetch(PDO::FETCH_ASSOC);

// İlana ait bütün fotoğrafları getir.
$fotografSorgu = $baglanti->prepare(
    "SELECT dosya_yolu
     FROM ilan_fotograflari
     WHERE ilan_id = :ilan_id
     ORDER BY id ASC"
);

$fotografSorgu->execute([
    ":ilan_id" => $ilan_id
]);

$fotograflar = $fotografSorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($ilan["baslik"], ENT_QUOTES, "UTF-8") ?> | OtoBul
    </title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f6fa;
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

        main {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .ilan-baslik {
            color: #123b66;
        }

        .fotograflar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }

        .fotograflar img {
            width: 100%;
            height: 230px;
            object-fit: cover;
            border-radius: 10px;
        }

        .ilan-bilgileri {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .bilgi {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .bilgi strong {
            color: #123b66;
        }

        .fiyat {
            font-size: 26px;
            color: #1769aa;
            font-weight: bold;
            margin: 20px 0;
        }

        .aciklama {
            white-space: pre-line;
            line-height: 1.6;
        }
    </style>
</head>

<body>

<header>
    <a class="logo" href="panel.php">OtoBul</a>
</header>

<main>

    <h1 class="ilan-baslik">
        <?= htmlspecialchars($ilan["baslik"], ENT_QUOTES, "UTF-8") ?>
    </h1>

    <div class="fiyat">
    <?= number_format((float) $ilan["fiyat"], 0, ",", ".") ?> TL

    <?php if ($ilan["kullanici_id"] != $_SESSION["kullanici_id"]): ?>

        <?php if ($favorideMi): ?>

    <form action="favori-sil.php" method="POST">
        <input type="hidden" name="ilan_id" value="<?= (int) $ilan["id"] ?>">

        <button type="submit">
            💔 Favorilerden Çıkar
        </button>
    </form>

        <?php else: ?>

            <form action="favori-ekle.php" method="POST">
                <input type="hidden" name="ilan_id" value="<?= (int) $ilan["id"] ?>">

                <button type="submit">
                     Favorilere Ekle
                </button>
            </form>

        <?php endif; ?>

    <?php else: ?>

        <p><strong>Bu ilan size ait.</strong></p>

    <?php endif; ?>
</div>

    <?php if (count($fotograflar) > 0): ?>

        <div class="fotograflar">

            <?php foreach ($fotograflar as $fotograf): ?>

                <img
                    src="<?= htmlspecialchars($fotograf["dosya_yolu"], ENT_QUOTES, "UTF-8") ?>"
                    alt="Araç fotoğrafı"
                >

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <p>Bu ilana ait fotoğraf bulunmamaktadır.</p>

    <?php endif; ?>

    <div class="ilan-bilgileri">

        <div class="bilgi">
            <strong>Marka:</strong>
            <?= htmlspecialchars($ilan["marka"], ENT_QUOTES, "UTF-8") ?>
        </div>

        <div class="bilgi">
            <strong>Model:</strong>
            <?= htmlspecialchars($ilan["model"], ENT_QUOTES, "UTF-8") ?>
        </div>

        <div class="bilgi">
            <strong>Motor:</strong>
            <?= htmlspecialchars($ilan["motor"], ENT_QUOTES, "UTF-8") ?>
        </div>

        <div class="bilgi">
            <strong>Model Yılı:</strong>
            <?= (int) $ilan["yil"] ?>
        </div>

        <div class="bilgi">
            <strong>Kilometre:</strong>
            <?= number_format((int) $ilan["kilometre"], 0, ",", ".") ?> km
        </div>

        <div class="bilgi">
            <strong>Yakıt:</strong>
            <?= htmlspecialchars($ilan["yakit"], ENT_QUOTES, "UTF-8") ?>
        </div>

        <div class="bilgi">
            <strong>Vites:</strong>
            <?= htmlspecialchars($ilan["vites"], ENT_QUOTES, "UTF-8") ?>
        </div>

        <div class="bilgi">
            <strong>Konum:</strong>
            <?= htmlspecialchars($ilan["il"], ENT_QUOTES, "UTF-8") ?>
            /
            <?= htmlspecialchars($ilan["ilce"], ENT_QUOTES, "UTF-8") ?>
        </div>

        <div class="bilgi">
            <strong>Boya / Değişen:</strong>
            <?= nl2br(htmlspecialchars($ilan["boya_degisen"], ENT_QUOTES, "UTF-8")) ?>
        </div>

        <div class="bilgi">
            <strong>İlan Açıklaması:</strong>

            <div class="aciklama">
                <?= htmlspecialchars($ilan["aciklama"], ENT_QUOTES, "UTF-8") ?>
            </div>
        </div>

    </div>

</main>



</body>
</html>