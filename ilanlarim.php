<?php
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı bu sayfayı açamaz.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Sadece giriş yapan kullanıcının ilanlarını getir.
$sorgu = $baglanti->prepare(
    "SELECT i.*,
        (
            SELECT f.dosya_yolu
            FROM ilan_fotograflari f
            WHERE f.ilan_id = i.id
            ORDER BY f.id ASC
            LIMIT 1
        ) AS kapak_fotografi
     FROM ilanlar i
     WHERE i.kullanici_id = :kullanici_id
     ORDER BY i.olusturma_tarihi DESC"
);

$sorgu->execute([
    ":kullanici_id" => $_SESSION["kullanici_id"]
]);

$ilanlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlanlarım | OtoBul</title>

    <style>
        .ilan-gor {
    display: inline-block;
    margin-top: 12px;
    padding: 10px 18px;
    background-color: #1769aa;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-weight: bold;
}

.ilan-gor:hover {
    background-color: #123b66;
}
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

        h2 {
            color: #123b66;
        }

        .ilan-listesi {
    display: grid;
    grid-template-columns: repeat(auto-fill, 380px);
    gap: 25px;
    margin-top: 30px;
    justify-content: start;
}

        .ilan-karti {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.10);
        }

        .ilan-karti img {
    width: 100%;
    height: 250px;
    object-fit: contain;
    background-color: #f1f1f1;
}

        .ilan-bilgileri {
            padding: 20px;
        }

        .ilan-bilgileri h3 {
            color: #123b66;
            margin-top: 0;
        }

        .ilan-bilgileri p {
            color: #555;
            margin: 8px 0;
        }

        .fiyat {
            color: #1769aa !important;
            font-size: 21px;
            font-weight: bold;
        }

        .fotograf-yok {
            height: 210px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9eef3;
            color: #666;
        }

        .ilan-yok {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            margin-top: 25px;
        }
    .ilan-islemleri {
    display: flex;
    gap: 8px;
    margin-top: 15px;
    flex-wrap: wrap;
    align-items: center;
}

.ilan-islemleri form {
    margin: 0;
}

.duzenle {
    display: inline-block;
    padding: 10px 18px;
    background-color: #f0ad4e;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-weight: bold;
}

.duzenle:hover {
    background-color: #d99638;
}

.sil {
    padding: 10px 18px;
    background-color: #d9534f;
    color: white;
    border: none;
    border-radius: 7px;
    font-weight: bold;
    cursor: pointer;
}

.sil:hover {
    background-color: #bd3f3b;
}




    </style>
</head>

<body>

<header>
    <a class="logo" href="panel.php">OtoBul</a>
</header>

<main>

    <h2>İlanlarım</h2>
    <p>Yayınladığınız otomobil ilanlarını buradan görüntüleyebilirsiniz.</p>

    <?php if (count($ilanlar) === 0): ?>

        <div class="ilan-yok">
            Henüz yayınlanmış bir ilanınız bulunmamaktadır.
        </div>

    <?php else: ?>

        <div class="ilan-listesi">

            <?php foreach ($ilanlar as $ilan): ?>

                <div class="ilan-karti">

                    <?php if (!empty($ilan["kapak_fotografi"])): ?>

                        <img
                            src="<?= htmlspecialchars($ilan["kapak_fotografi"], ENT_QUOTES, "UTF-8") ?>"
                            alt="İlan fotoğrafı"
                        >

                    <?php else: ?>

                        <div class="fotograf-yok">
                            Fotoğraf bulunamadı
                        </div>

                    <?php endif; ?>

                    <div class="ilan-bilgileri">

                        <h3>
                            <?= htmlspecialchars($ilan["baslik"], ENT_QUOTES, "UTF-8") ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($ilan["marka"], ENT_QUOTES, "UTF-8") ?>
                            <?= htmlspecialchars($ilan["model"], ENT_QUOTES, "UTF-8") ?>
                        </p>

                        <p>
                            <?= (int) $ilan["yil"] ?> |
                            <?= number_format((int) $ilan["kilometre"], 0, ",", ".") ?> km
                        </p>

                        <p>
                            <?= htmlspecialchars($ilan["il"], ENT_QUOTES, "UTF-8") ?>
                            /
                            <?= htmlspecialchars($ilan["ilce"], ENT_QUOTES, "UTF-8") ?>
                        </p>

                        <p class="fiyat">
                            <?= number_format((float) $ilan["fiyat"], 0, ",", ".") ?> TL
                        </p>
                        <div class="ilan-islemleri">

    <a class="ilan-gor"
       href="ilan-detay.php?id=<?= (int) $ilan["id"] ?>">
        İlanı Gör
    </a>

    <a class="duzenle"
       href="ilan-duzenle.php?id=<?= (int) $ilan["id"] ?>">
        Düzenle
    </a>

    <form
        action="ilan-sil.php"
        method="POST"
        onsubmit="return confirm('Bu ilanı silmek istediğinize emin misiniz?');"
    >
        <input
            type="hidden"
            name="ilan_id"
            value="<?= (int) $ilan["id"] ?>"
        >

        <button class="sil" type="submit">
            Sil
        </button>
    </form>

</div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

</body>
</html>