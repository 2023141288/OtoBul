<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı ilan kaydedemez.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Bu sayfaya sadece form gönderimiyle gelinsin.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ilan-ekle.php");
    exit;
}

// Formdan gelen bilgiler
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

// Zorunlu alanların kontrolü
if (
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

// İlanı veritabanına kaydet
$sorgu = $baglanti->prepare(
    "INSERT INTO ilanlar
    (
        kullanici_id,
        baslik,
        marka,
        model,
        motor,
        yil,
        kilometre,
        yakit,
        vites,
        fiyat,
        il,
        ilce,
        boya_degisen,
        aciklama
    )
    VALUES
    (
        :kullanici_id,
        :baslik,
        :marka,
        :model,
        :motor,
        :yil,
        :kilometre,
        :yakit,
        :vites,
        :fiyat,
        :il,
        :ilce,
        :boya_degisen,
        :aciklama
    )"
);

$sorgu->execute([
    ":kullanici_id" => $_SESSION["kullanici_id"],
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
    ":aciklama" => $aciklama
]);

// Yeni oluşan ilanın ID numarasını al.
$ilan_id = $baglanti->lastInsertId();
// Fotoğrafların kaydedileceği klasör
$yuklemeKlasoru = __DIR__ . "/uploads/ilanlar/";

// Klasör yoksa otomatik oluştur.
if (!is_dir($yuklemeKlasoru)) {
    mkdir($yuklemeKlasoru, 0777, true);
}

// İzin verilen fotoğraf türleri
$izinliTurler = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp"
];

if (isset($_FILES["fotograflar"])) {

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    foreach ($_FILES["fotograflar"]["tmp_name"] as $index => $geciciDosya) {

        // Kullanıcı bu sırada fotoğraf seçmemişse geç.
        if ($_FILES["fotograflar"]["error"][$index] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        // Yükleme sırasında hata oluşmuşsa geç.
        if ($_FILES["fotograflar"]["error"][$index] !== UPLOAD_ERR_OK) {
            continue;
        }

        // En fazla 5 MB
        if ($_FILES["fotograflar"]["size"][$index] > 5 * 1024 * 1024) {
            continue;
        }

        // Dosyanın gerçekten izin verilen bir görsel olup olmadığını kontrol et.
        $dosyaTuru = $finfo->file($geciciDosya);

        if (!isset($izinliTurler[$dosyaTuru])) {
            continue;
        }

        $uzanti = $izinliTurler[$dosyaTuru];

        // Aynı isimli dosyaların çakışmaması için benzersiz isim üret.
        $yeniDosyaAdi = bin2hex(random_bytes(16)) . "." . $uzanti;

        $tamYol = $yuklemeKlasoru . $yeniDosyaAdi;

        if (move_uploaded_file($geciciDosya, $tamYol)) {

            $veritabaniYolu = "uploads/ilanlar/" . $yeniDosyaAdi;

            $fotografSorgu = $baglanti->prepare(
                "INSERT INTO ilan_fotograflari (ilan_id, dosya_yolu)
                 VALUES (:ilan_id, :dosya_yolu)"
            );

            $fotografSorgu->execute([
                ":ilan_id" => $ilan_id,
                ":dosya_yolu" => $veritabaniYolu
            ]);
        }
    }
}

// Kayıt tamamlandıktan sonra panele dön.
header("Location: panel.php?ilan=basarili");
exit;