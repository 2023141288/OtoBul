
<?php
session_start();
// Admin işlemleri için güvenlik anahtarı
if (empty($_SESSION["admin_csrf_token"])) {
    $_SESSION["admin_csrf_token"] = bin2hex(random_bytes(32));
}
require_once "baglanti.php";

// Giriş yapılmış mı?
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// İşlem sadece form üzerinden yapılabilir.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit;
}

// Formun güvenlik anahtarını kontrol et.
$token = $_POST["csrf_token"] ?? "";

if (
    !is_string($token) ||
    empty($_SESSION["admin_csrf_token"]) ||
    !hash_equals($_SESSION["admin_csrf_token"], $token)
) {
    http_response_code(403);
    exit("Geçersiz veya yetkisiz işlem isteği.");
}


// Kullanıcının admin olup olmadığını kontrol et.
$adminSorgu = $baglanti->prepare(
    "SELECT rol FROM kullanicilar WHERE id = :id"
);

$adminSorgu->execute([
    ":id" => $_SESSION["kullanici_id"]
]);

$rol = $adminSorgu->fetchColumn();

if ($rol !== "admin") {
    http_response_code(403);
    exit("Bu işlemi yapmaya yetkiniz yok.");
}

// Formdan gelen bilgiler
$ilan_id = filter_input(
    INPUT_POST,
    "ilan_id",
    FILTER_VALIDATE_INT
);

$islem = $_POST["islem"] ?? "";

if (!$ilan_id || !in_array($islem, ["onayla", "reddet"], true)) {
    exit("Geçersiz işlem bilgileri.");
}

// Butona göre yeni durumu belirle.
$yeniDurum = $islem === "onayla"
    ? "onaylandi"
    : "reddedildi";

// Sadece bekleyen ilanların durumunu değiştir.
$guncelle = $baglanti->prepare(
    "UPDATE ilanlar
     SET durum = :durum
     WHERE id = :ilan_id
       AND durum = 'beklemede'"
);

$guncelle->execute([
    ":durum" => $yeniDurum,
    ":ilan_id" => $ilan_id
]);

header("Location: admin.php");
exit;
