
<?php
session_start();

require_once "baglanti.php";

// Kullanıcı giriş yapmamışsa onu giriş sayfasına gönder.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// İşlem sadece form üzerinden yapılabilir. GET ve POST, 
// tarayıcının sunucuya istek göndermek için kullandığı HTTP yöntemleridir.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit;
}

// Formun güvenlik anahtarını kontrol et. Bu kod CSRF güvenlik kontrolü yapıyor. Yani başka bir sitenin, yönetici adına izinsiz işlem yaptırmasını önlemeye yardımcı oluyor.
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

// Formdan ret gerekçesini al.
$retGerekcesi = $_POST["ret_gerekcesi"] ?? "";

if (!is_string($retGerekcesi)) {
    exit("Geçersiz ret gerekçesi.");
}

$retGerekcesi = trim($retGerekcesi);

// İlan reddediliyorsa gerekçe zorunlu olsun.
if ($islem === "reddet" && $retGerekcesi === "") {
    exit("İlanı reddetmek için gerekçe yazmalısınız.");
}

// İlan onaylanıyorsa ret gerekçesi kaydedilmesin.
if ($islem === "onayla") {
    $retGerekcesi = null;
}

// İlanın durumunu ve ret gerekçesini güncelle.
$guncelle = $baglanti->prepare(
    "UPDATE ilanlar
     SET durum = :durum,
         ret_gerekcesi = :ret_gerekcesi
     WHERE id = :ilan_id
       AND durum = 'beklemede'"
);

$guncelle->execute([
    ":durum" => $yeniDurum,
    ":ret_gerekcesi" => $retGerekcesi,
    ":ilan_id" => $ilan_id
]);

header("Location: admin.php");
exit;
