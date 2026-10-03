
<?php
session_start();
require_once "baglanti.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: giris.html");
    exit;
}

$email = trim($_POST["email"] ?? "");
$sifre = $_POST["sifre"] ?? "";

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $sifre === "") {
    exit("E-posta veya şifre hatalı.");
}

$sorgu = $baglanti->prepare(
    "SELECT id, ad_soyad, email, sifre
     FROM kullanicilar
     WHERE email = :email"
);

$sorgu->execute([":email" => $email]);
$kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

if ($kullanici && password_verify($sifre, $kullanici["sifre"])) {
    session_regenerate_id(true);

    $_SESSION["kullanici_id"] = $kullanici["id"];
    $_SESSION["ad_soyad"] = $kullanici["ad_soyad"];

    header("Location: panel.php");
exit;
} else {
    echo "E-posta veya şifre hatalı.";
}
