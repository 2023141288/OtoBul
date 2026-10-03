<?php
session_start();
require_once "baglanti.php";

// Giriş yapmamış kullanıcı Hesabım sayfasını açamaz.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Giriş yapan kullanıcının bilgilerini veritabanından al.
$sorgu = $baglanti->prepare(
    "SELECT ad_soyad, email
     FROM kullanicilar
     WHERE id = :id"
);

$sorgu->execute([
    ":id" => $_SESSION["kullanici_id"]
]);

$kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    exit("Kullanıcı bilgileri bulunamadı.");
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesabım | OtoBul</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f6fa;
        }

        .ust-menu {
            background-color: #1769aa;
            padding: 16px 8%;
            display: flex;
            align-items: center;
        }

        .logo {
            background-color: white;
            color: #1769aa;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
        }

        .hesap-karti {
            background-color: white;
            width: 420px;
            max-width: 80%;
            margin: 70px auto;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.10);
        }

        .hesap-karti h2 {
            color: #123b66;
            margin-top: 0;
        }

        .bilgi {
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }

        .bilgi strong {
            color: #1769aa;
        }

        .cikis {
            display: block;
            margin-top: 30px;
            padding: 12px;
            background-color: #1769aa;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .cikis:hover {
            background-color: #123b66;
        }
    </style>
</head>

<body>

<header class="ust-menu">
    <a class="logo" href="panel.php">OtoBul</a>
</header>

<div class="hesap-karti">

    <h2>Hesap Bilgilerim</h2>

    <div class="bilgi">
        <strong>Ad Soyad:</strong>
        <?= htmlspecialchars($kullanici["ad_soyad"], ENT_QUOTES, "UTF-8") ?>
    </div>

    <div class="bilgi">
        <strong>E-posta:</strong>
        <?= htmlspecialchars($kullanici["email"], ENT_QUOTES, "UTF-8") ?>
    </div>

    <a class="cikis" href="cikis.php">Çıkış Yap</a>

</div>

</body>
</html>