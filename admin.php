
<?php
session_start(); // Oturum bilgileri kullanmaya başlar.
if (empty($_SESSION["admin_csrf_token"])) { 
    $_SESSION["admin_csrf_token"] = bin2hex(random_bytes(32)); // random_bytes(32): Rastgele güvenli veri üretir

    // bin2hex(): Veriyi hexadecimal metne çevirir.
}
require_once "baglanti.php";  // MySQL veritabanı bağlantısını sağlayan dosyayı dahil eder.



// Giriş yapmayan kullanıcı admin paneline giremez.
if (!isset($_SESSION["kullanici_id"])) { 
    header("Location: giris.html");
    exit;
}

// Giriş yapan kullanıcının rolünü veritabanından öğren.  // FROM: Hangi tablodan alınacağını belirtir.
// WHERE: Kullanıcı ID'sine göre filtreler.
$sorgu = $baglanti->prepare(
    "SELECT ad_soyad, rol
     FROM kullanicilar       
     WHERE id = :kullanici_id"
);

$sorgu->execute([              // execute(): Sorguyu çalıştırır.
    ":kullanici_id" => $_SESSION["kullanici_id"]   // :kullanici_id: Oturumdaki kullanıcı ID'siyle eşleştirilir.
]);

$kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);   // Kullanıcının bir satırlık bilgisini sütun adlarıyla alır.

// Admin olmayan kullanıcıları engelle.
if (!$kullanici || $kullanici["rol"] !== "admin") {   // Kullanıcı bulunamazsa veya admin değilse erişimi engeller.
    http_response_code(403);                            // ||: VEYA anlamına gelir.
                                                        // !==: Eşit değil anlamına gelir.
   exit("Bu sayfaya erişim yetkiniz yok.");
}

    // Yönetici panelinde seçilen ilan durumunu belirle.
$seciliDurum = $_GET["durum"] ?? "beklemede";

// Sadece izin verilen durumlar kullanılabilir.
if (!in_array($seciliDurum, ["beklemede", "onaylandi", "reddedildi"], true)) {
    $seciliDurum = "beklemede";
}


$ilanSorgu = $baglanti->prepare(
    "SELECT id, baslik, marka, model, yil, kilometre,
            fiyat, aciklama, boya_degisen,

            (SELECT COUNT(*)
             FROM ilan_fotograflari
             WHERE ilan_id = ilanlar.id) AS fotograf_sayisi

     FROM ilanlar
     WHERE durum = :durum
     ORDER BY id DESC"
);
// WHERE: Sadece belirtilen durumdaki ilanları getirir.
// ORDER BY id DESC: ID'ye göre büyükten küçüğe sıralar.





$ilanSorgu->execute([
    ":durum" => $seciliDurum
]);


$bekleyenIlanlar = $ilanSorgu->fetchAll(PDO::FETCH_ASSOC);  // Sorgudan gelen bütün ilanları diziye aktarır.

// Onay bekleyen ilanların fotoğraflarını getir.
$fotografSorgu = $baglanti->prepare(
    "SELECT f.ilan_id, f.dosya_yolu
     FROM ilan_fotograflari f
     INNER JOIN ilanlar i ON f.ilan_id = i.id
     WHERE i.durum = :durum
     ORDER BY f.ilan_id, f.id"
);

$fotografSorgu->execute([
    ":durum" => $seciliDurum
]);

$tumFotograflar = $fotografSorgu->fetchAll(PDO::FETCH_ASSOC);

// Fotoğrafları ait oldukları ilanlara göre grupla.
$ilanFotograflari = [];

foreach ($tumFotograflar as $fotograf) {
    $ilanFotograflari[$fotograf["ilan_id"]][] = $fotograf["dosya_yolu"];
}


?>

<!DOCTYPE html>  
<html lang="tr">
<head>
    <meta charset="UTF-8">    <!-- Türkçe karakterlerin düzgün görünmesini sağlar. -->



    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Sayfanın mobil ekranlara uyumunu destekler. -->
    <title>OtoBul - Admin Paneli</title>  

    <style>   /* Sayfanın genel yazı tipi, arka planı ve boşlukları. */
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        header { /* Üst başlık alanının görünümü. */
            background: #1769aa;
            color: white;
            padding: 20px 30px;
        }

        main {
            max-width: 1000px;
            margin: 35px auto;
            padding: 20px;
        }

        .kutu {  /* İçerik kutusunun görünümü. */
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px #00000012;
        }

        a {
            color: #1769aa;
        }
    </style>
</head>

<body>

<header>
    <h2>OtoBul | Yönetici Paneli</h2>
</header>

<main>
    <div class="kutu">
        <h2>
            Hoş geldiniz,
            <?= htmlspecialchars($kullanici["ad_soyad"], ENT_QUOTES, "UTF-8") ?> <!-- htmlspecialchars(): Özel HTML karakterlerini güvenli hale getirir. -->
        </h2>

        <p>Bu alan yalnızca site yöneticilerine özeldir.</p>

        <h3>Yönetim İşlemleri</h3>
<div style="display:flex; gap:12px; margin:20px 0; flex-wrap:wrap;">

    <a href="admin.php?durum=beklemede">
        🟡 Bekleyenler
    </a>

    <a href="admin.php?durum=onaylandi">
        🟢 Onaylananlar
    </a>

    <a href="admin.php?durum=reddedildi">
        🔴 Reddedilenler
    </a>

</div>


        
<?php if (empty($bekleyenIlanlar)): ?>

    <p>Şu anda onay bekleyen ilan bulunmuyor.</p>

<?php else: ?>
    <div style="overflow-x:auto;">
    <table border="1" cellpadding="12"
           style="width:100%; border-collapse:collapse;">

        <tr>
            <th>İlan No</th>
            <th>Başlık</th>
            <th>Marka / Model</th>
            <th>Yıl</th>
            <th>Kilometre</th>
            <th>Fiyat</th>
            <th>Boya / Değişen</th>
            <th>Açıklama</th>
            <th>Fotoğraf Sayısı</th>
            <th>Fotoğraflar</th>
            <?php if ($seciliDurum === "beklemede"): ?>
    <th>İşlemler</th>
<?php endif; ?>
        </tr>

        <?php foreach ($bekleyenIlanlar as $ilan): ?>
            <tr>
                <td><?= (int) $ilan["id"] ?></td>

                <td>
                    <?= htmlspecialchars($ilan["baslik"], ENT_QUOTES, "UTF-8") ?>
                </td>

                <td>
                    <?= htmlspecialchars($ilan["marka"] . " " . $ilan["model"], ENT_QUOTES, "UTF-8") ?>
                </td>

                <td><?= (int) $ilan["yil"] ?></td>


<td>
    <?= number_format((int) $ilan["kilometre"], 0, ",", ".") ?> km
</td>


                <td>
                    <?= number_format((float) $ilan["fiyat"], 0, ",", ".") ?> TL
                </td>
                <td>
    <?php
    $boyaDegisen = $ilan["boya_degisen"] ?? "";
    $uzunMu = preg_match('/^(.{100})(.+)$/us', $boyaDegisen, $parcalar);
    ?>

    <?php if ($uzunMu): ?>
        <details>
            <summary style="cursor:pointer;">
                <?= htmlspecialchars($parcalar[1], ENT_QUOTES, "UTF-8") ?>...
                <span style="color:#1769aa;">Devamını Gör</span>
            </summary>

            <p>
                <?= nl2br(htmlspecialchars($boyaDegisen, ENT_QUOTES, "UTF-8")) ?>
            </p>
        </details>
    <?php else: ?>
        <?= nl2br(htmlspecialchars($boyaDegisen, ENT_QUOTES, "UTF-8")) ?>
    <?php endif; ?>
</td>

                <td>
    <?php
    $aciklama = $ilan["aciklama"] ?? "";
    $uzunMu = preg_match('/^(.{100})(.+)$/us', $aciklama, $parcalar);
    ?>

    <?php if ($uzunMu): ?>
        <details>
            <summary style="cursor:pointer;">
                <?= htmlspecialchars($parcalar[1], ENT_QUOTES, "UTF-8") ?>...
                <span style="color:#1769aa;">Devamını Gör</span>
            </summary>

            <p>
                <?= nl2br(htmlspecialchars($aciklama, ENT_QUOTES, "UTF-8")) ?>
            </p>
        </details>
    <?php else: ?>
        <?= nl2br(htmlspecialchars($aciklama, ENT_QUOTES, "UTF-8")) ?>
    <?php endif; ?>
</td>

<td>
    <?= (int) $ilan["fotograf_sayisi"] ?> adet
</td>

<td>
    <details>
        <summary style="cursor:pointer; color:#1769aa;">
            📷 Fotoğrafları Gör
        </summary>

        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;">

            <?php foreach ($ilanFotograflari[$ilan["id"]] ?? [] as $foto): ?>

                <img
                    src="<?= htmlspecialchars($foto, ENT_QUOTES, "UTF-8") ?>"
                    alt="Araç fotoğrafı"
                    style="width:150px; height:110px;
                           object-fit:cover; border-radius:8px;"
                >

            <?php endforeach; ?>

        </div>
    </details>
</td>


<?php if ($seciliDurum === "beklemede"): ?>
                
<td>  <!-- İşlem bilgilerini PHP dosyasına POST ile gönderir. -->
    <form action="admin-ilan-islem.php" method="POST"  
          style="display:inline-block;"> 
          <input type="hidden" name="csrf_token"
       value="<?= htmlspecialchars($_SESSION["admin_csrf_token"], ENT_QUOTES, "UTF-8") ?>">

        <input type="hidden" name="ilan_id"
               value="<?= (int) $ilan["id"] ?>">

        <div style="margin:10px 0;">
    <label for="ret-gerekcesi-<?= (int) $ilan["id"] ?>">
        Ret Gerekçesi:
    </label>

    <textarea
        id="ret-gerekcesi-<?= (int) $ilan["id"] ?>"
        name="ret_gerekcesi"
        rows="2"
        placeholder="İlan neden reddediliyor?"
        style="width:100%; box-sizing:border-box;
               padding:8px; margin-top:5px;"
    ></textarea>
    <p class="ret-uyari" style="display:none; color:red; font-size:13px;">
    Lütfen ret gerekçesi yazınız.
</p>
</div>

<button type="submit" name="islem" value="onayla"
        style="background:#218838; color:white;
               border:0; padding:9px;
               border-radius:5px; cursor:pointer;">
    ✓ Onayla
</button>
        </button>

        <button type="submit" name="islem" value="reddet"
        onclick="if (!this.form.ret_gerekcesi.value.trim()) {
    this.form.querySelector('.ret-uyari').style.display = 'block';
    return false;
}"
                
                style="background:#c0392b; color:white;
                       border:0; padding:9px;
                       border-radius:5px; cursor:pointer;">
            ✕ Reddet
        </button>
    </form>
</td>
<?php endif; ?>

            </tr>
        <?php endforeach; ?>

    </table>
    </div>

<?php endif; ?>


        <a href="panel.php">← Kullanıcı Paneline Dön</a>
    </div>
</main>

</body>
</html>
