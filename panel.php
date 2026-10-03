
<?php
session_start();

// Giriş yapmayan kullanıcıyı giriş sayfasına gönder.
if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

$ad_soyad = $_SESSION["ad_soyad"];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Paneli | OtoBul</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f6fa;
            text-align: center;
        }

        header {
            background-color: #1769aa;
            color: white;
            padding: 24px;
        }

        main {
            padding: 60px 20px;
        }

        .secenekler {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 35px;
            flex-wrap: wrap;
        }

        .secenekler a {
            background-color: #1769aa;
            color: white;
            padding: 22px 35px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .secenekler a:hover {
            background-color: #123b66;
        }

        
.ust-menu {
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 16px 8%;
}

.logo {
    background: white;
    color: #1769aa;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 22px;
    font-weight: bold;
    text-decoration: none;
}

.arama {
    flex: 1;
    min-width: 100px;
    padding: 12px;
    border: 0;
    border-radius: 6px;
}

.cikis {
    color: white;
    text-decoration: none;
}



.yapay-zeka {
    background-color: white;
    max-width: 520px;
    margin: 50px auto;
    padding: 25px;
    border: 1px solid #c8daed;
    border-radius: 12px;
}

.yapay-zeka h3 {
    color: #1769aa;
}

.yapay-zeka p {
    color: #555;
    line-height: 1.6;
}

.yapay-zeka span {
    color: #1769aa;
    font-weight: bold;
}

.panel-alani {
    display: flex;
    min-height: calc(100vh - 80px);
}

.sol-menu {
    width: 220px;
    background-color: white;
    padding: 30px 20px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
    text-align: left;
}

.sol-menu h3 {
    color: #123b66;
    margin-top: 0;
    margin-bottom: 25px;
}

.sol-menu a {
    display: block;
    color: #333;
    text-decoration: none;
    padding: 13px 10px;
    margin-bottom: 8px;
    border-radius: 7px;
}

.sol-menu a:hover {
    background-color: #eaf2f8;
    color: #1769aa;
}

.ana-icerik {
    flex: 1;
    padding: 60px 20px;
}

.hesap-menu {
    position: relative;
    color: white;
}

.hesap-menu summary {
    cursor: pointer;
    list-style: none;
    white-space: nowrap;
}

.hesap-sekmeleri {
    position: absolute;
    right: 0;
    top: 35px;
    width: 190px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    z-index: 100;
}

.hesap-sekmeleri a {
    display: block;
    padding: 13px 15px;
    color: #333;
    text-decoration: none;
    text-align: left;
}

.hesap-sekmeleri a:hover {
    background-color: #eaf2f8;
    color: #1769aa;
}

    </style>
</head>

<body>
    
<header class="ust-menu">
    <a class="logo" href="panel.php">OtoBul</a>

    <input class="arama"
           type="search"
           placeholder="Marka veya model ara">

    <details class="hesap-menu">
    <summary>Hesabım ▼</summary>

    <div class="hesap-sekmeleri">
        <a href="hesabim.php">👤 Hesap Bilgilerim</a>
        <a href="cikis.php">🚪 Çıkış Yap</a>
    </div>
</details>
</header>


    <div class="panel-alani">

    <aside class="sol-menu">
        <h3>Hızlı Menü</h3>

        <a href="#">🚗 İlanlarım</a>
        <a href="#">❤️ Favori İlanlarım</a>
        <a href="#">💬 Mesajlarım</a>
    </aside>

    <main class="ana-icerik">
        <h2>Hoş geldin,
            <?= htmlspecialchars($ad_soyad, ENT_QUOTES, "UTF-8") ?>!
        </h2>

        <p>Ne yapmak istiyorsun?</p>

        <div class="secenekler">
            <a href="araclar.php">🚘 Araç Al</a>
            <a href="ilan-ekle.php">🔑 Araç Sat</a>
        </div>
        
<section class="yapay-zeka">
    <h3>🤖 Araçlardan pek anlamıyor musunuz?</h3>

    <p>
        Yakında OtoBul Yapay Zekâ Asistanı ile
        bütçenize ve ihtiyaçlarınıza uygun
        otomobili bulmanıza yardımcı olacağız.
    </p>

    <span>Çok yakında</span>
</section>

        </main>

</div>

</body>
</html>
