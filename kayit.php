
<?php

require_once "baglanti.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    

    $ad_soyad = trim($_POST["ad_soyad"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $sifre = $_POST["sifre"] ?? "";

    if ($ad_soyad === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($sifre) < 8) {
        exit("Bilgileri kontrol edin. Şifre en az 8 karakter olmalı.");
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

        echo "Kaydınız başarıyla oluşturuldu.";

    } catch (PDOException $hata) {
        if ($hata->getCode() === "23000") {
            exit("Bu e-posta adresi zaten kayıtlı.");
        }

        error_log($hata->getMessage());
        exit("Kayıt sırasında bir hata oluştu.");
    }
} else {
    header("Location: kayit.html");
    exit;
}
