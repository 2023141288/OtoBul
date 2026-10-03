<?php
session_start();
require_once "baglanti.php";

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: favorilerim.php");
    exit;
}

$ilan_id = (int) ($_POST["ilan_id"] ?? 0);

if ($ilan_id <= 0) {
    exit("Geçersiz ilan.");
}

$sil = $baglanti->prepare(
    "DELETE FROM favoriler
     WHERE kullanici_id = :kullanici_id
     AND ilan_id = :ilan_id"
);

$sil->execute([
    ":kullanici_id" => $_SESSION["kullanici_id"],
    ":ilan_id" => $ilan_id
]);

header("Location: ilan-detay.php?id=" . $ilan_id);
exit;