<?php
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı düzenleme yapamaz.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Adresten ilan ID'sini al.
$ilan_id = (int) ($_GET["id"] ?? 0);

if ($ilan_id <= 0) {
    exit("Geçersiz ilan.");
}

// İlanı getir.
// kullanici_id kontrolü sayesinde kullanıcı sadece kendi ilanını düzenleyebilir.
$sorgu = $baglanti->prepare(
    "SELECT *
     FROM ilanlar
     WHERE id = :id
     AND kullanici_id = :kullanici_id"
);

$sorgu->execute([
    ":id" => $ilan_id,
    ":kullanici_id" => $_SESSION["kullanici_id"]
]);

$ilan = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$ilan) {
    exit("İlan bulunamadı veya bu ilanı düzenleme yetkiniz yok.");
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>İlanı Düzenle | OtoBul</title>

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
            max-width: 700px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            color: #123b66;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 7px;
            font-weight: bold;
            color: #333;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: 0;
            border-radius: 8px;
            background-color: #1769aa;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #123b66;
        }
    </style>
</head>

<body>

<header>
    <a class="logo" href="panel.php">OtoBul</a>
</header>

<main>

    <h2>İlanı Düzenle</h2>

    <form action="ilan-guncelle.php" method="POST">

        <!-- Hangi ilanın güncelleneceğini gizli olarak gönderiyoruz. -->
        <input
            type="hidden"
            name="ilan_id"
            value="<?= (int) $ilan["id"] ?>"
        >

        <label>İlan Başlığı</label>
        <input
            type="text"
            name="baslik"
            value="<?= htmlspecialchars($ilan["baslik"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>Marka</label>
        <input
            type="text"
            name="marka"
            value="<?= htmlspecialchars($ilan["marka"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>Model</label>
        <input
            type="text"
            name="model"
            value="<?= htmlspecialchars($ilan["model"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>Motor</label>
        <input
            type="text"
            name="motor"
            value="<?= htmlspecialchars($ilan["motor"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>Model Yılı</label>
        <input
            type="number"
            name="yil"
            min="1900"
            max="2100"
            value="<?= (int) $ilan["yil"] ?>"
            required
        >

        <label>Kilometre</label>
        <input
            type="number"
            name="kilometre"
            min="0"
            value="<?= (int) $ilan["kilometre"] ?>"
            required
        >

        <label>Yakıt</label>
        <input
            type="text"
            name="yakit"
            value="<?= htmlspecialchars($ilan["yakit"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>Vites</label>
        <input
            type="text"
            name="vites"
            value="<?= htmlspecialchars($ilan["vites"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>Fiyat</label>
        <input
            type="number"
            name="fiyat"
            min="1"
            step="0.01"
            value="<?= htmlspecialchars($ilan["fiyat"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>İl</label>
        <input
            type="text"
            name="il"
            value="<?= htmlspecialchars($ilan["il"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>İlçe</label>
        <input
            type="text"
            name="ilce"
            value="<?= htmlspecialchars($ilan["ilce"], ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label>Aracın Boya / Değişen Durumu</label>
        <textarea
            name="boya_degisen"
            rows="4"
            required
        ><?= htmlspecialchars($ilan["boya_degisen"], ENT_QUOTES, "UTF-8") ?></textarea>

        <label>İlan Açıklaması</label>
        <textarea
            name="aciklama"
            rows="6"
        ><?= htmlspecialchars($ilan["aciklama"], ENT_QUOTES, "UTF-8") ?></textarea>

        <button type="submit">
            Değişiklikleri Kaydet
        </button>

    </form>

</main>

</body>
</html>