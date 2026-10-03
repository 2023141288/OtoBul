<?php
session_start();
require_once "baglanti.php";

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

$marka = trim($_GET["marka"] ?? "");
$model = trim($_GET["model"] ?? "");
$min_yil = (int) ($_GET["min_yil"] ?? 0);
$max_yil = (int) ($_GET["max_yil"] ?? 0);
$min_fiyat = (float) ($_GET["min_fiyat"] ?? 0);
$max_fiyat = (float) ($_GET["max_fiyat"] ?? 0);
$max_km = (int) ($_GET["max_km"] ?? 0);
$yakit = trim($_GET["yakit"] ?? "");
$vites = trim($_GET["vites"] ?? "");
$il = trim($_GET["il"] ?? "");
$kosullar = [];
$parametreler = [];
if ($marka !== "") {
    $kosullar[] = "i.marka LIKE :marka";
    $parametreler[":marka"] = "%" . $marka . "%";
}

if ($model !== "") {
    $kosullar[] = "i.model LIKE :model";
    $parametreler[":model"] = "%" . $model . "%";
}
if ($min_yil > 0) {
    $kosullar[] = "i.yil >= :min_yil";
    $parametreler[":min_yil"] = $min_yil;
}

if ($max_yil > 0) {
    $kosullar[] = "i.yil <= :max_yil";
    $parametreler[":max_yil"] = $max_yil;
}

if ($min_fiyat > 0) {
    $kosullar[] = "i.fiyat >= :min_fiyat";
    $parametreler[":min_fiyat"] = $min_fiyat;
}

if ($max_fiyat > 0) {
    $kosullar[] = "i.fiyat <= :max_fiyat";
    $parametreler[":max_fiyat"] = $max_fiyat;
}

if ($max_km > 0) {
    $kosullar[] = "i.kilometre <= :max_km";
    $parametreler[":max_km"] = $max_km;
}
if ($yakit !== "") {
    $kosullar[] = "i.yakit = :yakit";
    $parametreler[":yakit"] = $yakit;
}

if ($vites !== "") {
    $kosullar[] = "i.vites = :vites";
    $parametreler[":vites"] = $vites;
}

if ($il !== "") {
    $kosullar[] = "i.il LIKE :il";
    $parametreler[":il"] = "%" . $il . "%";
}
$where = "";

if (!empty($kosullar)) {
    $where = "WHERE " . implode(" AND ", $kosullar);
}


$sorgu = $baglanti->prepare(
    "SELECT i.*,
        (
            SELECT f.dosya_yolu
            FROM ilan_fotograflari f
            WHERE f.ilan_id = i.id
            ORDER BY f.kapak_mi DESC, f.id ASC
            LIMIT 1
        ) AS kapak_fotografi
     FROM ilanlar i
     $where
     ORDER BY i.olusturma_tarihi DESC"
);

$sorgu->execute($parametreler);
$ilanlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araç Al - OtoBul</title>
<style>
.ilan-listesi {
    display: grid;
    grid-template-columns: repeat(auto-fill, 380px);
    gap: 25px;
    padding: 25px;
}

.ilan-karti {
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    background-color: white;
    padding-bottom: 20px;
}

.ilan-karti img {
    width: 100%;
    height: 250px;
    object-fit: contain;
    background-color: #f1f1f1;
}

.ilan-karti h2,
.ilan-karti p,
.ilan-karti strong,
.ilan-karti a {
    margin-left: 15px;
}
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f7fa;
}

h1 {
    color: #123b66;
    margin: 30px 25px 15px;
}

.filtre-formu {
    margin: 0 25px 10px;
    padding: 20px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);

    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.filtre-formu input,
.filtre-formu select {
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

.filtre-formu button {
    padding: 11px;
    background-color: #1769aa;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
}

.filtre-formu button:hover {
    background-color: #12558a;
}


</style>

</head>

<body>

<h1>Satılık Araçlar</h1>
<form class="filtre-formu" method="GET" action="araclar.php">

    <input type="text" name="marka" placeholder="Marka">
    <input type="text" name="model" placeholder="Model">

    <input type="number" name="min_yil" placeholder="En düşük yıl">
    <input type="number" name="max_yil" placeholder="En yüksek yıl">

    <input type="number" name="min_fiyat" placeholder="En düşük fiyat">
    <input type="number" name="max_fiyat" placeholder="En yüksek fiyat">

    <input type="number" name="max_km" placeholder="En fazla kilometre">

    <select name="yakit">
        <option value="">Yakıt fark etmez</option>
        <option value="Benzin">Benzin</option>
        <option value="Dizel">Dizel</option>
        <option value="LPG">LPG</option>
        <option value="Elektrik">Elektrik</option>
        <option value="Hibrit">Hibrit</option>
    </select>

    <select name="vites">
        <option value="">Vites fark etmez</option>
        <option value="Manuel">Manuel</option>
        <option value="Otomatik">Otomatik</option>
        <option value="Yarı Otomatik">Yarı Otomatik</option>
    </select>

    <input type="text" name="il" placeholder="İl">

    <button type="submit">Araçları Göster</button>

</form>

<div class="ilan-listesi">

    <?php foreach ($ilanlar as $ilan): ?>

        <div class="ilan-karti">

            <?php if (!empty($ilan["kapak_fotografi"])): ?>
                <img
                    src="<?= htmlspecialchars($ilan["kapak_fotografi"]) ?>"
                    alt="Araç fotoğrafı"
                >
            <?php endif; ?>

            <h2><?= htmlspecialchars($ilan["baslik"]) ?></h2>

            <p>
                <?= htmlspecialchars($ilan["marka"]) ?>
                <?= htmlspecialchars($ilan["model"]) ?>
            </p>

            <p>
                <?= (int) $ilan["yil"] ?> |
                <?= number_format($ilan["kilometre"], 0, ",", ".") ?> km
            </p>

            <p>
                <?= htmlspecialchars($ilan["il"]) ?> /
                <?= htmlspecialchars($ilan["ilce"]) ?>
            </p>

            <strong>
                <?= number_format($ilan["fiyat"], 0, ",", ".") ?> TL
            </strong>

            <br><br>

            <a href="ilan-detay.php?id=<?= (int) $ilan["id"] ?>">
                İlanı İncele
            </a>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>


