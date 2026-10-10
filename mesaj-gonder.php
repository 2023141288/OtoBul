<?php

session_start();
require_once "baglanti.php";

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: araclar.php");
    exit;
}

$ilan_id = (int) ($_POST["ilan_id"] ?? 0);
$mesaj = trim($_POST["mesaj"] ?? "");

if ($ilan_id <= 0 || $mesaj === "") {
    exit("Geçersiz ilan veya boş mesaj.");
}
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

$alici_id = (int) $ilan["kullanici_id"];

if ($alici_id === (int) $_SESSION["kullanici_id"]) {
    exit("Kendi ilanınıza mesaj gönderemezsiniz.");
}
$mesajEkle = $baglanti->prepare(
    "INSERT INTO mesajlar
        (ilan_id, gonderen_id, alici_id, mesaj)
     VALUES
        (:ilan_id, :gonderen_id, :alici_id, :mesaj)"
);

$mesajEkle->execute([
    ":ilan_id" => $ilan_id,
    ":gonderen_id" => $_SESSION["kullanici_id"],
    ":alici_id" => $alici_id,
    ":mesaj" => $mesaj
]);

header("Location: ilan-detay.php?id=" . $ilan_id);
exit;