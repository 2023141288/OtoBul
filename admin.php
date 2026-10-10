
<?php
session_start();
if (empty($_SESSION["admin_csrf_token"])) {
    $_SESSION["admin_csrf_token"] = bin2hex(random_bytes(32));
}
require_once "baglanti.php";

// Giriş yapmayan kullanıcı admin paneline giremez.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Giriş yapan kullanıcının rolünü veritabanından öğren.
$sorgu = $baglanti->prepare(
    "SELECT ad_soyad, rol
     FROM kullanicilar
     WHERE id = :kullanici_id"
);

$sorgu->execute([
    ":kullanici_id" => $_SESSION["kullanici_id"]
]);

$kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

// Admin olmayan kullanıcıları engelle.
if (!$kullanici || $kullanici["rol"] !== "admin") {
    http_response_code(403);
    exit("Bu sayfaya erişim yetkiniz yok.");
}


$ilanSorgu = $baglanti->prepare(
    "SELECT id, baslik, marka, model, yil, fiyat
     FROM ilanlar
     WHERE durum = :durum
     ORDER BY id DESC"
);

$ilanSorgu->execute([
    ":durum" => "beklemede"
]);

$bekleyenIlanlar = $ilanSorgu->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OtoBul - Admin Paneli</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        header {
            background: #1769aa;
            color: white;
            padding: 20px 30px;
        }

        main {
            max-width: 1000px;
            margin: 35px auto;
            padding: 20px;
        }

        .kutu {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px #00000012;
        }

        a {
            color: #1769aa;
        }
    </style>
</head>

<body>

<header>
    <h2>OtoBul | Yönetici Paneli</h2>
</header>

<main>
    <div class="kutu">
        <h2>
            Hoş geldiniz,
            <?= htmlspecialchars($kullanici["ad_soyad"], ENT_QUOTES, "UTF-8") ?>
        </h2>

        <p>Bu alan yalnızca site yöneticilerine özeldir.</p>

        <h3>Yönetim İşlemleri</h3>
        
<?php if (empty($bekleyenIlanlar)): ?>

    <p>Şu anda onay bekleyen ilan bulunmuyor.</p>

<?php else: ?>

    <table border="1" cellpadding="12"
           style="width:100%; border-collapse:collapse;">

        <tr>
            <th>İlan No</th>
            <th>Başlık</th>
            <th>Marka / Model</th>
            <th>Yıl</th>
            <th>Fiyat</th>
            <th>İşlemler</th>
        </tr>

        <?php foreach ($bekleyenIlanlar as $ilan): ?>
            <tr>
                <td><?= (int) $ilan["id"] ?></td>

                <td>
                    <?= htmlspecialchars($ilan["baslik"], ENT_QUOTES, "UTF-8") ?>
                </td>

                <td>
                    <?= htmlspecialchars($ilan["marka"] . " " . $ilan["model"], ENT_QUOTES, "UTF-8") ?>
                </td>

                <td><?= (int) $ilan["yil"] ?></td>

                <td>
                    <?= number_format((float) $ilan["fiyat"], 0, ",", ".") ?> TL
                </td>
                
<td>
    <form action="admin-ilan-islem.php" method="POST"
          style="display:inline-block;">
          <input type="hidden" name="csrf_token"
       value="<?= htmlspecialchars($_SESSION["admin_csrf_token"], ENT_QUOTES, "UTF-8") ?>">

        <input type="hidden" name="ilan_id"
               value="<?= (int) $ilan["id"] ?>">

        <button type="submit" name="islem" value="onayla"
                style="background:#218838; color:white;
                       border:0; padding:9px;
                       border-radius:5px; cursor:pointer;">
            ✓ Onayla
        </button>

        <button type="submit" name="islem" value="reddet"
                onclick="return confirm('Bu ilanı reddetmek istiyor musunuz?');"
                style="background:#c0392b; color:white;
                       border:0; padding:9px;
                       border-radius:5px; cursor:pointer;">
            ✕ Reddet
        </button>
    </form>
</td>

            </tr>
        <?php endforeach; ?>

    </table>

<?php endif; ?>


        <a href="panel.php">← Kullanıcı Paneline Dön</a>
    </div>
</main>

</body>
</html>
