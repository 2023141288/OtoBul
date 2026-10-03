<?php
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı güncelleme yapamaz.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Bu sayfaya sadece form gönderimiyle gelinsin.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ilanlarim.php");
    exit;
}

// Formdan gelen bilgiler
$ilan_id = (int) ($_POST["ilan_id"] ?? 0);
$baslik = trim($_POST["baslik"] ?? "");
$marka = trim($_POST["marka"] ?? "");
$model = trim($_POST["model"] ?? "");
$motor = trim($_POST["motor"] ?? "");
$yil = (int) ($_POST["yil"] ?? 0);
$kilometre = (int) ($_POST["kilometre"] ?? 0);
$yakit = trim($_POST["yakit"] ?? "");
$vites = trim($_POST["vites"] ?? "");
$fiyat = (float) ($_POST["fiyat"] ?? 0);
$il = trim($_POST["il"] ?? "");
$ilce = trim($_POST["ilce"] ?? "");
$boya_degisen = trim($_POST["boya_degisen"] ?? "");
$aciklama = trim($_POST["aciklama"] ?? "");

// Temel kontroller
if (
    $ilan_id <= 0 ||
    $baslik === "" ||
    $marka === "" ||
    $model === "" ||
    $motor === "" ||
    $yil < 1900 ||
    $yil > 2100 ||
    $kilometre < 0 ||
    $yakit === "" ||
    $vites === "" ||
    $fiyat <= 0 ||
    $il === "" ||
    $ilce === "" ||
    $boya_degisen === ""
) {
    exit("Lütfen ilan bilgilerini eksiksiz ve doğru giriniz.");
}

// İlanı güncelle.
// kullanici_id kontrolü sayesinde kullanıcı sadece kendi ilanını güncelleyebilir.
$sorgu = $baglanti->prepare(
    "UPDATE ilanlar
     SET
        baslik = :baslik,
        marka = :marka,
        model = :model,
        motor = :motor,
        yil = :yil,
        kilometre = :kilometre,
        yakit = :yakit,
        vites = :vites,
        fiyat = :fiyat,
        il = :il,
        ilce = :ilce,
        boya_degisen = :boya_degisen,
        aciklama = :aciklama
     WHERE id = :ilan_id
     AND kullanici_id = :kullanici_id"
);

$sorgu->execute([
    ":baslik" => $baslik,
    ":marka" => $marka,
    ":model" => $model,
    ":motor" => $motor,
    ":yil" => $yil,
    ":kilometre" => $kilometre,
    ":yakit" => $yakit,
    ":vites" => $vites,
    ":fiyat" => $fiyat,
    ":il" => $il,
    ":ilce" => $ilce,
    ":boya_degisen" => $boya_degisen,
    ":aciklama" => $aciklama,
    ":ilan_id" => $ilan_id,
    ":kullanici_id" => $_SESSION["kullanici_id"]
]);

// Güncelleme sonrası ilan detayına dön.
header("Location: ilan-detay.php?id=" . $ilan_id);
exit;