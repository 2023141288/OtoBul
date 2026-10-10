
<?php
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı cevap gönderemez.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Yalnızca POST isteklerini kabul et.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: mesajlarim.php");
    exit;
}

// Formdan gelen bilgileri al.
$gonderen_id = (int) $_SESSION["kullanici_id"];
$ilan_id = (int) ($_POST["ilan_id"] ?? 0);
$alici_id = (int) ($_POST["kisi_id"] ?? 0);
$mesaj = trim($_POST["mesaj"] ?? "");

// Bilgileri kontrol et.
if (
    $ilan_id <= 0 ||
    $alici_id <= 0 ||
    $mesaj === "" ||
    $gonderen_id === $alici_id
) {
    exit("Geçersiz mesaj bilgileri.");
}

if (strlen($mesaj) > 8000) {
    exit("Mesaj çok uzun.");
}

// İlanın sahibini öğren.
$ilanSorgu = $baglanti->prepare(
    "SELECT kullanici_id
     FROM ilanlar
     WHERE id = :ilan_id"
);

$ilanSorgu->execute([
    ":ilan_id" => $ilan_id
]);

$ilan = $ilanSorgu->fetch(PDO::FETCH_ASSOC);

if (!$ilan) {
    exit("İlan bulunamadı.");
}

$ilan_sahibi = (int) $ilan["kullanici_id"];

// Konuşan iki kişiden biri ilan sahibi olmalı.
if (
    $ilan_sahibi !== $gonderen_id &&
    $ilan_sahibi !== $alici_id
) {
    exit("Bu konuşmaya cevap verme yetkiniz yok.");
}

// Bu iki kullanıcı arasında önceden mesaj var mı?
$kontrol = $baglanti->prepare(
    "SELECT id
     FROM mesajlar
     WHERE ilan_id = :ilan_id
       AND (
           (gonderen_id = :ben1 AND alici_id = :diger1)
           OR
           (gonderen_id = :diger2 AND alici_id = :ben2)
       )
     LIMIT 1"
);

$kontrol->execute([
    ":ilan_id" => $ilan_id,
    ":ben1" => $gonderen_id,
    ":diger1" => $alici_id,
    ":diger2" => $alici_id,
    ":ben2" => $gonderen_id
]);

if (!$kontrol->fetch(PDO::FETCH_ASSOC)) {
    exit("Konuşma bulunamadı veya erişim yetkiniz yok.");
}

// Cevabı veritabanına kaydet.
$mesajEkle = $baglanti->prepare(
    "INSERT INTO mesajlar
        (ilan_id, gonderen_id, alici_id, mesaj)
     VALUES
        (:ilan_id, :gonderen_id, :alici_id, :mesaj)"
);

$mesajEkle->execute([
    ":ilan_id" => $ilan_id,
    ":gonderen_id" => $gonderen_id,
    ":alici_id" => $alici_id,
    ":mesaj" => $mesaj
]);

// Konuşma ekranına geri dön.
header(
    "Location: mesaj-detay.php?ilan_id=" .
    $ilan_id . "&kisi_id=" . $alici_id .
    "&cevap=basarili"
);
exit;
