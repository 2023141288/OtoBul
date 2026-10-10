
<?php
session_start();
require_once "baglanti.php";

// Giriş yapmayan kullanıcı sayfayı açamaz.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

// Adresten gelen bilgiler
$kullanici_id = (int) $_SESSION["kullanici_id"];
$ilan_id = (int) ($_GET["ilan_id"] ?? 0);
$kisi_id = (int) ($_GET["kisi_id"] ?? 0);

if ($ilan_id <= 0 || $kisi_id <= 0 ||
    $kisi_id === $kullanici_id) {
    exit("Geçersiz konuşma.");
}


$gizlemeSorgu = $baglanti->prepare(
    "SELECT son_gizlenen_mesaj_id
     FROM gizlenen_konusmalar
     WHERE kullanici_id = :kullanici_id
       AND ilan_id = :ilan_id
       AND diger_kullanici_id = :kisi_id"
);

$gizlemeSorgu->execute([
    ":kullanici_id" => $kullanici_id,
    ":ilan_id" => $ilan_id,
    ":kisi_id" => $kisi_id
]);

$son_gizlenen_id = (int) (
    $gizlemeSorgu->fetchColumn() ?: 0
);


// Yalnızca iki kullanıcı arasındaki mesajları getir.

$sorgu = $baglanti->prepare(
    "SELECT *
     FROM mesajlar
     WHERE ilan_id = :ilan_id
       AND id > :son_gizlenen_id
       AND (
           (gonderen_id = :ben1 AND alici_id = :diger1)
           OR
           (gonderen_id = :diger2 AND alici_id = :ben2)
       )
     ORDER BY gonderim_tarihi ASC, id ASC"
);

$sorgu->execute([
    ":ilan_id" => $ilan_id,
    ":son_gizlenen_id" => $son_gizlenen_id,
    ":ben1" => $kullanici_id,
    ":diger1" => $kisi_id,
    ":diger2" => $kisi_id,
    ":ben2" => $kullanici_id
]);


$mesajlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// Başkasının konuşmasını görüntülemeyi engelle.
if (empty($mesajlar)) {
    exit("Konuşma bulunamadı veya erişim yetkiniz yok.");
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konuşma | OtoBul</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f6fa;
            margin: 0;
        }

        header {
            background: #1769aa;
            padding: 20px;
        }

        header a {
            color: white;
            text-decoration: none;
        }

        main {
            max-width: 750px;
            margin: 35px auto;
            padding: 15px;
        }

        .mesaj {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }

        .benim-mesajim {
            background: #dceeff;
            margin-left: 40px;
        }

        .diger-mesaj {
            margin-right: 40px;
        }

        .mesaj p {
            overflow-wrap: anywhere;
            white-space: pre-wrap;
        }

        small {
            color: #666;
        }
    </style>
</head>

<body>

<header>
    <a href="mesajlarim.php">← Mesajlarıma Dön</a>
</header>

<main>
    <h2>💬 Konuşma</h2>

    <?php foreach ($mesajlar as $mesaj): ?>

        <div class="mesaj <?= (int) $mesaj["gonderen_id"] === $kullanici_id
            ? "benim-mesajim"
            : "diger-mesaj" ?>">

            <strong>
                <?= (int) $mesaj["gonderen_id"] === $kullanici_id
                    ? "Siz"
                    : "Diğer Kullanıcı" ?>
            </strong>

            <p><?= htmlspecialchars(
                $mesaj["mesaj"],
                ENT_QUOTES,
                "UTF-8"
            ) ?></p>

            <small>
                <?= htmlspecialchars(
                    $mesaj["gonderim_tarihi"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </small>

        </div>

    <?php endforeach; ?>

    
<h3>Mesaj Yaz</h3>

<form action="mesaj-cevap.php" method="POST">

    <input type="hidden"
           name="ilan_id"
           value="<?= $ilan_id ?>">

    <input type="hidden"
           name="kisi_id"
           value="<?= $kisi_id ?>">

    <textarea name="mesaj"
              rows="4"
              maxlength="2000"
              placeholder="Mesajınızı yazın..."
              required
              style="width:100%; padding:12px; box-sizing:border-box;"></textarea>

    <button type="submit"
            style="background:#1769aa; color:white;
                   padding:12px 25px; border:0;
                   border-radius:7px; margin-top:10px;
                   cursor:pointer;">
        Cevap Gönder
    </button>

</form>


</main>

</body>
</html>
