<?php
session_start();
require_once "baglanti.php";

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}

$marka = trim($_GET["marka"] ?? "");
$model = trim($_GET["model"] ?? "");
$motor = trim($_GET["motor"] ?? "");
$min_yil = (int) ($_GET["min_yil"] ?? 0);
$max_yil = (int) ($_GET["max_yil"] ?? 0);
$min_fiyat = (float) ($_GET["min_fiyat"] ?? 0);
$max_fiyat = (float) ($_GET["max_fiyat"] ?? 0);
$max_km = (int) ($_GET["max_km"] ?? 0);
$yakit = trim($_GET["yakit"] ?? "");
$vites = trim($_GET["vites"] ?? "");
$il = trim($_GET["il"] ?? "");
$kosullar = ["i.durum = 'onaylandi'"];
$parametreler = [];
if ($marka !== "") {
    $kosullar[] = "i.marka LIKE :marka";
    $parametreler[":marka"] = "%" . $marka . "%";
}

if ($model !== "") {
    $kosullar[] = "i.model LIKE :model";
    $parametreler[":model"] = "%" . $model . "%";
}
    if ($motor !== "") {
    $kosullar[] = "i.motor LIKE :motor";
    $parametreler[":motor"] = $motor . "%";
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


<h1>
    <a href="panel.php" style="display:block; font-size:16px; margin-bottom:12px; color:#1769aa; text-decoration:none;">
        ← Ana Sayfaya Dön
    </a>
    Satılık Araçlar
</h1>

<form class="filtre-formu" method="GET" action="araclar.php">

    <input type="text" name="marka" id="marka"
       list="marka-listesi" placeholder="Marka"
       value="<?= htmlspecialchars($marka) ?>">

<datalist id="marka-listesi">
    <option value="Hyundai">
    <option value="Fiat">
    <option value="Renault">
    <option value="Toyota">
    <option value="Volkswagen">
    <option value="Ford">
    <option value="Honda">
    <option value="BMW">
    <option value="Mercedes-Benz">
    <option value="Opel">
    <option value="Peugeot">
    <option value="Citroen">
</datalist>

<input type="text" name="model" id="model"
       list="model-listesi" placeholder="Model"
       value="<?= htmlspecialchars($model) ?>">

<datalist id="model-listesi"></datalist>
    <select name="motor">
    <option value="">Motor hacmi fark etmez</option>
    <option value="0.9">0.9</option>
    <option value="1.0">1.0</option>
    <option value="1.2">1.2</option>
    <option value="1.3">1.3</option>
    <option value="1.4">1.4</option>
    <option value="1.5">1.5</option>
    <option value="1.6">1.6</option>
    <option value="1.8">1.8</option>
    <option value="2.0">2.0</option>
    <option value="2.2">2.2</option>
    <option value="2.5">2.5</option>
    <option value="3.0">3.0</option>
</select>
    <input type="number" name="min_yil" placeholder="En düşük yıl">
    <input type="number" name="max_yil" placeholder="En yüksek yıl">

    <input type="number" name="min_fiyat" placeholder="En düşük fiyat">
    <input type="number" name="max_fiyat" placeholder="En yüksek fiyat">

    <input type="number" name="max_km" placeholder="En fazla kilometre">

    <select name="yakit">
        <option value="">Yakıt fark etmez</option>
        <option value="Benzin / LPG">Benzin / LPG</option>
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

    <select id="il" name="il">
    <option value="">İl fark etmez</option>
</select>

<select id="ilce" name="ilce" disabled>
    <option value="">Önce il seçiniz</option>
</select>

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
                <script>
const modeller = {
    "Hyundai": ["Accent era","i20", "i30", "Elantra", "Bayon", "Tucson","i10","Accent Blue"],
    "Fiat": ["Egea", "Linea", "Doblo", "Punto", "Palio"],
    "Renault": ["Clio", "Megane", "Symbol", "Fluence", "Captur"],
    "Toyota": ["Corolla", "Yaris", "C-HR", "Auris","Avensis"],
    "Volkswagen": ["Golf", "Passat", "Polo", "Jetta", "Tiguan"],
    "Ford": ["Focus", "Fiesta", "Mondeo", "Kuga"],
    "Honda": ["Civic", "City", "Jazz", "CR-V"],
    "BMW": ["3 Serisi", "5 Serisi", "1 Serisi", "X5"],
    "Mercedes-Benz": ["C Serisi", "E Serisi", "A Serisi", "CLA"],
    "Opel": ["Astra", "Corsa", "Insignia", "Mokka"],
    "Peugeot": ["208", "308", "3008", "508"],
    "Citroen": ["C3", "C4", "C5 Aircross", "Berlingo"]
};

const markaInput = document.getElementById("marka");
const modelInput = document.getElementById("model");
const modelListesi = document.getElementById("model-listesi");

function modelleriGuncelle() {
    const marka = markaInput.value.trim().toLocaleLowerCase("tr");
    modelListesi.innerHTML = "";

    const secilenMarka = Object.keys(modeller).find(
        m => m.toLocaleLowerCase("tr") === marka
    );

    if (secilenMarka) {
        modeller[secilenMarka].forEach(function(model) {
            const option = document.createElement("option");
            option.value = model;
            modelListesi.appendChild(option);
        });
    }
}

markaInput.addEventListener("input", function() {
    modelInput.value = "";
    modelleriGuncelle();
});

modelleriGuncelle();
</script>
                <script>
fetch("il_ilce.json")
    .then(cevap => cevap.json())
    .then(veriler => {

        const ilSecimi = document.getElementById("il");
        const ilceSecimi = document.getElementById("ilce");

        // İlleri listeye ekle
        veriler.forEach(function(sehir) {
            const secenek = document.createElement("option");
            secenek.value = sehir.il;
            secenek.textContent = sehir.il;
            ilSecimi.appendChild(secenek);
        });

        // İl seçildiğinde ilçeleri getir
        ilSecimi.addEventListener("change", function() {

            ilceSecimi.innerHTML =
                '<option value="">İlçe fark etmez</option>';

            ilceSecimi.disabled = true;

            const secilenSehir = veriler.find(function(sehir) {
                return sehir.il === ilSecimi.value;
            });

            if (secilenSehir) {
                secilenSehir.ilceler.forEach(function(ilce) {
                    const secenek = document.createElement("option");
                    secenek.value = ilce;
                    secenek.textContent = ilce;
                    ilceSecimi.appendChild(secenek);
                });

                ilceSecimi.disabled = false;
            }
        });
    })
    .catch(hata => console.error("İl ve ilçeler yüklenemedi:", hata));
</script>
</body>
</html>


