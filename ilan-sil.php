<?php
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı ilan silemez.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Silme işlemi sadece POST isteğiyle yapılabilir.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ilanlarim.php");
    exit;
}

$ilan_id = (int) ($_POST["ilan_id"] ?? 0);

if ($ilan_id <= 0) {
    exit("Geçersiz ilan.");
}

// İlanın gerçekten giriş yapan kullanıcıya ait olup olmadığını kontrol et.
$kontrol = $baglanti->prepare(
    "SELECT id
     FROM ilanlar
     WHERE id = :ilan_id
     AND kullanici_id = :kullanici_id"
);

$kontrol->execute([
    ":ilan_id" => $ilan_id,
    ":kullanici_id" => $_SESSION["kullanici_id"]
]);

if (!$kontrol->fetch(PDO::FETCH_ASSOC)) {
    exit("Bu ilanı silme yetkiniz yok.");
}

// Fiziksel fotoğraf dosyalarını daha sonra silebilmek için yollarını al.
$fotografSorgu = $baglanti->prepare(
    "SELECT dosya_yolu
     FROM ilan_fotograflari
     WHERE ilan_id = :ilan_id"
);

$fotografSorgu->execute([
    ":ilan_id" => $ilan_id
]);

$fotograflar = $fotografSorgu->fetchAll(PDO::FETCH_ASSOC);

// İlanı veritabanından sil.
$sil = $baglanti->prepare(
    "DELETE FROM ilanlar
     WHERE id = :ilan_id
     AND kullanici_id = :kullanici_id"
);

$sil->execute([
    ":ilan_id" => $ilan_id,
    ":kullanici_id" => $_SESSION["kullanici_id"]
]);

// Veritabanından silme başarılıysa yüklenen fotoğraf dosyalarını da sil.
if ($sil->rowCount() > 0) {

    foreach ($fotograflar as $fotograf) {

        $dosya = __DIR__ . "/" . $fotograf["dosya_yolu"];

        if (is_file($dosya)) {
            unlink($dosya);
        }
    }
}

header("Location: ilanlarim.php");
exit;