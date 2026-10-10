
<?php
session_start();
require_once "baglanti.php";

// Kullanıcı giriş yapmış mı?
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Yalnızca POST isteği kabul et.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: mesajlarim.php");
    exit;
}

// Güvenlik anahtarını kontrol et.
$token = $_POST["csrf_token"] ?? "";

if (
    !is_string($token) ||
    empty($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    exit("Geçersiz işlem isteği.");
}

// Formdan gelen bilgiler
$kullanici_id = (int) $_SESSION["kullanici_id"];
$ilan_id = (int) ($_POST["ilan_id"] ?? 0);
$kisi_id = (int) ($_POST["kisi_id"] ?? 0);

if (
    $ilan_id <= 0 ||
    $kisi_id <= 0 ||
    $kisi_id === $kullanici_id
) {
    exit("Geçersiz konuşma bilgileri.");
}

// Bu iki kullanıcı arasında mesaj var mı?
$sorgu = $baglanti->prepare(
    "SELECT MAX(id) AS son_mesaj_id
     FROM mesajlar
     WHERE ilan_id = :ilan_id
       AND (
           (gonderen_id = :ben1 AND alici_id = :diger1)
           OR
           (gonderen_id = :diger2 AND alici_id = :ben2)
       )"
);

$sorgu->execute([
    ":ilan_id" => $ilan_id,
    ":ben1" => $kullanici_id,
    ":diger1" => $kisi_id,
    ":diger2" => $kisi_id,
    ":ben2" => $kullanici_id
]);

$son_mesaj_id = (int) $sorgu->fetchColumn();

if ($son_mesaj_id <= 0) {
    exit("Konuşma bulunamadı.");
}

// Konuşmayı yalnızca bu kullanıcı için gizle.
$gizle = $baglanti->prepare(
    "INSERT INTO gizlenen_konusmalar
        (kullanici_id, ilan_id,
         diger_kullanici_id, son_gizlenen_mesaj_id)
     VALUES
        (:kullanici_id, :ilan_id,
         :diger_kullanici_id, :son_mesaj_id)
     ON DUPLICATE KEY UPDATE
        son_gizlenen_mesaj_id =
        GREATEST(
            son_gizlenen_mesaj_id,
            VALUES(son_gizlenen_mesaj_id)
        )"
);

$gizle->execute([
    ":kullanici_id" => $kullanici_id,
    ":ilan_id" => $ilan_id,
    ":diger_kullanici_id" => $kisi_id,
    ":son_mesaj_id" => $son_mesaj_id
]);

header("Location: mesajlarim.php");
exit;
