
<?php

require_once "baglanti.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    

    $ad_soyad = trim($_POST["ad_soyad"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $sifre = $_POST["sifre"] ?? "";

    if ($ad_soyad === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || strlen($sifre) < 8) {

    header("Location: kayit.html?hata=bilgiler");
    exit;
}

    $sifreli_sifre = password_hash($sifre, PASSWORD_DEFAULT);

    try {
        $sorgu = $baglanti->prepare(
            "INSERT INTO kullanicilar (ad_soyad, email, sifre)
             VALUES (:ad_soyad, :email, :sifre)"
        );

        $sorgu->execute([
            ":ad_soyad" => $ad_soyad,
            ":email" => $email,
            ":sifre" => $sifreli_sifre
        ]);

        header("Location: kayit.html?basarili=1");
exit;

    } catch (PDOException $hata) {
        if ($hata->getCode() === "23000") {
    header("Location: kayit.html?hata=eposta");
    exit;
}

        error_log($hata->getMessage());
        exit("Kayıt sırasında bir hata oluştu.");
    }
} else {
    header("Location: kayit.html");
    exit;
}
