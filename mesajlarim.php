
<?php
session_start();
// Silme işlemleri için güvenlik anahtarı oluştur.
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
require_once "baglanti.php";

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

$kullanici_id = (int) $_SESSION["kullanici_id"];


$sorgu = $baglanti->prepare(
    "SELECT
        m.*,
        i.baslik,
        g.ad_soyad AS gonderen_adi,
        a.ad_soyad AS alici_adi
     FROM mesajlar m

     INNER JOIN ilanlar i
        ON m.ilan_id = i.id

     INNER JOIN kullanicilar g
        ON m.gonderen_id = g.id

     INNER JOIN kullanicilar a
        ON m.alici_id = a.id

     LEFT JOIN gizlenen_konusmalar z
        ON z.kullanici_id = :gizleyen_id
        AND z.ilan_id = m.ilan_id
        AND z.diger_kullanici_id =
            CASE
                WHEN m.gonderen_id = :ben_kim
                THEN m.alici_id
                ELSE m.gonderen_id
            END

     WHERE (
        m.gonderen_id = :gonderen_id
        OR m.alici_id = :alici_id
     )
     AND m.id > COALESCE(
         z.son_gizlenen_mesaj_id, 0
     )

     ORDER BY m.gonderim_tarihi DESC, m.id DESC"
);

$sorgu->execute([
    ":gizleyen_id" => $kullanici_id,
    ":ben_kim" => $kullanici_id,
    ":gonderen_id" => $kullanici_id,
    ":alici_id" => $kullanici_id
]);


$mesajlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesajlarım | OtoBul</title>

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
            font-weight: bold;
        }

        main {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .mesaj-karti {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .mesaj-karti p {
            line-height: 1.6;
        }

        .tur {
            color: #1769aa;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <a href="panel.php">← OtoBul Paneline Dön</a>
</header>

<main>
    <h2>💬 Mesajlarım</h2>

    <?php if (empty($mesajlar)): ?>
        <p>Henüz mesajınız bulunmuyor.</p>
    <?php else: ?>

        <?php foreach ($mesajlar as $mesaj): ?>

            <div class="mesaj-karti">

                <p class="tur">
                    <?= (int) $mesaj["gonderen_id"] === $kullanici_id
                        ? "Gönderilen Mesaj"
                        : "Gelen Mesaj" ?>
                </p>

                <p>
                    <strong>İlan:</strong>
                    <?= htmlspecialchars($mesaj["baslik"], ENT_QUOTES, "UTF-8") ?>
                </p>

                <p>
                    <strong>Gönderen:</strong>
                    <?= htmlspecialchars($mesaj["gonderen_adi"], ENT_QUOTES, "UTF-8") ?>
                </p>

                <p>
                    <strong>Alıcı:</strong>
                    <?= htmlspecialchars($mesaj["alici_adi"], ENT_QUOTES, "UTF-8") ?>
                </p>

                <p>
                    <?= nl2br(htmlspecialchars($mesaj["mesaj"], ENT_QUOTES, "UTF-8")) ?>
                </p>

                <small>
                    <?= htmlspecialchars($mesaj["gonderim_tarihi"], ENT_QUOTES, "UTF-8") ?>
                </small>
                
<p>
    <a href="mesaj-detay.php?ilan_id=<?= (int) $mesaj["ilan_id"] ?>&kisi_id=<?= (int) (
        $mesaj["gonderen_id"] == $kullanici_id
            ? $mesaj["alici_id"]
            : $mesaj["gonderen_id"]
    ) ?>">
        💬 Konuşmayı Aç / Cevap Ver
        
<form action="konusma-sil.php" method="POST"
      onsubmit="return confirm('Bu konuşmayı listenizden kaldırmak istiyor musunuz?');">

    <input type="hidden" name="ilan_id"
           value="<?= (int) $mesaj["ilan_id"] ?>">

    <input type="hidden" name="kisi_id"
           value="<?= (int) (
               $mesaj["gonderen_id"] == $kullanici_id
                   ? $mesaj["alici_id"]
                   : $mesaj["gonderen_id"]
           ) ?>">

    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>">

    <button type="submit"
            style="background:#c0392b; color:white;
                   border:0; padding:10px 15px;
                   border-radius:6px; cursor:pointer;">
         Konuşmayı Sil
    </button>

</form>

    </a>
</p>


            </div>

        <?php endforeach; ?>

    <?php endif; ?>
</main>

</body>
</html>
