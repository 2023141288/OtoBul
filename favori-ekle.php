<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "baglanti.php";

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

$ilan_id = (int) ($_POST["ilan_id"] ?? 0);

if ($ilan_id <= 0) {
    exit("Geçersiz ilan.");
}
$kontrol = $baglanti->prepare(
    "SELECT kullanici_id
     FROM ilanlar
     WHERE id = :ilan_id"
);

$kontrol->execute([
    ":ilan_id" => $ilan_id
]);

$ilan = $kontrol->fetch(PDO::FETCH_ASSOC);

if (!$ilan) {
    exit("İlan bulunamadı.");
}

if ($ilan["kullanici_id"] == $_SESSION["kullanici_id"]) {
    exit("Kendi ilanınızı favorilere ekleyemezsiniz.");
}
$favori = $baglanti->prepare(
    "INSERT IGNORE INTO favoriler (kullanici_id, ilan_id)
     VALUES (:kullanici_id, :ilan_id)"
);

$favori->execute([
    ":kullanici_id" => $_SESSION["kullanici_id"],
    ":ilan_id" => $ilan_id
]);

header("Location: ilan-detay.php?id=" . $ilan_id);
exit;