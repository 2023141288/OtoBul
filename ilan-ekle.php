
<?php
session_start();

if (!isset($_SESSION["kullanici_id"])) {
    header("Location: giris.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlan Ver | OtoBul</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f6fa;
            margin: 0;
        }

        header {
            background-color: #1769aa;
            padding: 20px;
        }

        header a {
            color: white;
            text-decoration: none;
            font-size: 22px;
            font-weight: bold;
        }

        main {
            max-width: 650px;
            margin: 35px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 25px;
            padding: 12px 25px;
            background-color: #1769aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #124f80;
        }

        #fotograf-onizleme {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

.fotograf-secim {
    width: 150px;
    padding: 8px;
    border: 2px solid transparent;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    background-color: white;
}

.fotograf-secim img {
    width: 150px;
    height: 110px;
    object-fit: cover;
    border-radius: 7px;
}

.fotograf-secim.kapak {
    border-color: #1769aa;
}

.kapak-yazisi {
    margin-top: 6px;
    font-size: 14px;
    font-weight: bold;
    color: #555;
}

.fotograf-secim.kapak .kapak-yazisi {
    color: #1769aa;
}
    </style>
</head>

<body>
    <header>
        <a href="panel.php">OtoBul</a>
    </header>

    <main>
        <h2>Otomobilinizin İlanını Verin</h2>
        <p>Aracınızın bilgilerini aşağıya giriniz.</p>

<form action="ilan-kaydet.php" method="POST" enctype="multipart/form-data">           
     <label for="baslik">İlan Başlığı</label>
            <input
                type="text"
                id="baslik"
                name="baslik"
                placeholder="Örn: 2018 Hyundai i20"
                required
            >

            <label for="marka">Marka</label>
            <input
                type="text"
                id="marka"
                name="marka"
                list="markalar"
                placeholder="Marka seçin veya yazın"
                autocomplete="off"
                required
            >

            <datalist id="markalar">
                <option value="Hyundai">
                <option value="Renault">
                <option value="Fiat">
                <option value="Toyota">
                <option value="Volkswagen">
                <option value="Honda">
                <option value="Ford">
                <option value="BMW">
                <option value="Mercedes-Benz">
                <option value="Audi">
                <option value="Opel">
                <option value="Peugeot">
                <option value="Citroen">
                <option value="Skoda">
                <option value="Seat">
                <option value="Kia">
                <option value="Nissan">
                <option value="Dacia">
                <option value="Volvo">
            </datalist>

            <label for="model">Model</label>
            <input
                type="text"
                id="model"
                name="model"
                list="modeller"
                placeholder="Önce marka seçiniz"
                autocomplete="off"
                required
            >

            <datalist id="modeller"></datalist>

            
<label for="motor">Motor Seçeneği</label>
<input
    type="text"
    id="motor"
    name="motor"
    list="motorlar"
    placeholder="Motor seçin veya kendiniz yazın"
    required
>

<datalist id="motorlar">
    <option value="1.0 T-GDI">
    <option value="1.2 MPI">
    <option value="1.4 MPI">
    <option value="1.4 CVVT">
    <option value="1.5 dCi">
    <option value="1.6">
    <option value="1.6 CRDi">
    <option value="1.3 Multijet">
    <option value="1.6 TDI">
    <option value="2.0 TDI">
</datalist>


            <label for="yil">Model Yılı</label>
            <input
                type="number"
                id="yil"
                name="yil"
                placeholder="Örn: 2018"
                min="1900"
                max="2100"
                required
            >

            <label for="kilometre">Kilometre</label>
            <input
                type="number"
                id="kilometre"
                name="kilometre"
                placeholder="Örn: 85000"
                min="0"
                required
            >

            <label for="yakit">Yakıt Türü</label>
            <select id="yakit" name="yakit" required>
                <option value="">Yakıt türü seçiniz</option>
                <option value="Benzin">Benzin</option>
                <option value="Dizel">Dizel</option>
                <option value="Benzin / LPG">Benzin / LPG</option>
                <option value="Elektrik">Elektrik</option>
                <option value="Hibrit">Hibrit</option>
            </select>

            <label for="vites">Vites</label>
            <select id="vites" name="vites" required>
                <option value="">Vites seçiniz</option>
                <option value="Manuel">Manuel</option>
                <option value="Otomatik">Otomatik</option>
                <option value="Yarı Otomatik">Yarı Otomatik</option>
            </select>

            <label for="fiyat">Fiyat (TL)</label>
            <input
                type="number"
                id="fiyat"
                name="fiyat"
                placeholder="Örn: 750000"
                min="1"
                required
            >

            <label for="il">İl</label>
            <input
                type="text"
                id="il"
                name="il"
                placeholder="Örn: Sivas"
                required
            >

            <label for="ilce">İlçe</label>
            <input
                type="text"
                id="ilce"
                name="ilce"
                placeholder="Örn: Merkez"
                required
            >


        <label for="boya_degisen">Aracın Boya / Değişen Durumunu Yazınız</label>
<textarea
    id="boya_degisen"
    name="boya_degisen"
    rows="4"
    placeholder="Aracınızın boyalı ve değişen parçalarını yazınız..."
    required
></textarea>


            <label for="aciklama">İlan Açıklaması</label>
            <textarea
                id="aciklama"
                name="aciklama"
                rows="5"
                placeholder="Aracınız hakkında bilgi veriniz..."
            ></textarea>

            <label for="fotograflar">Araç Fotoğrafları</label>
            <input
                type="file"
                id="fotograflar"
                name="fotograflar[]"
                accept="image/*"
                multiple
            >
            <input type="hidden" name="kapak_index" id="kapak_index" value="0">

<div id="fotograf-onizleme"></div>

<button type="submit">İlanı Yayınla</button>        </form>
    </main>

    <script>
        // Her markanın modellerini burada tutuyoruz.
        const aracModelleri = {
            "Hyundai": [
                "Accent", "Accent Blue", "Accent Era",
                "i10", "i20", "i30", "Elantra",
                "Bayon", "Kona"
            ],
            "Renault": [
                "Clio", "Megane", "Fluence",
                "Symbol", "Talisman", "Captur",
            
            ],
            "Fiat": [
                "Egea", "Linea", "Punto",
                "Palio", "Albea", "500"
            ],
            "Toyota": [
                "Corolla", "Yaris", "Auris",
                "Avensis", "C-HR", "Camry"
            ],
            "Volkswagen": [
                "Golf", "Polo", "Passat",
                "Jetta", "Tiguan", "T-Roc"
            ],
            "Honda": [
                "Civic", "City", "Jazz",
                "Accord", "HR-V", "CR-V"
            ]
            
        };

        const markaAlani = document.getElementById("marka");
        const modelAlani = document.getElementById("model");
        const modelListesi = document.getElementById("modeller");

        markaAlani.addEventListener("input", function () {
            // Marka değişince eski modeli temizle.
            modelAlani.value = "";

            // Önceki markanın modellerini sil.
            modelListesi.innerHTML = "";

            // Kullanıcının yazdığı markayı bul.
            const secilenMarka = Object.keys(aracModelleri).find(
                function (markaAdi) {
                    return markaAdi.toLocaleLowerCase("tr-TR") ===
                        markaAlani.value.trim().toLocaleLowerCase("tr-TR");
                }
            );

            // Marka listemizde varsa ilgili modelleri ekle.
            if (secilenMarka) {
                aracModelleri[secilenMarka].forEach(
                    function (modelAdi) {
                        const secenek = document.createElement("option");
                        secenek.value = modelAdi;
                        modelListesi.appendChild(secenek);
                    }
                );
            }

            // Listede olmayan modeller de elle yazılabilir.
            modelAlani.placeholder = "Model seçin veya yazın";
        });
    </script>
<script>
const fotografInput = document.getElementById("fotograflar");
const onizleme = document.getElementById("fotograf-onizleme");
const kapakIndex = document.getElementById("kapak_index");

fotografInput.addEventListener("change", function () {

    onizleme.innerHTML = "";
    kapakIndex.value = 0;

    Array.from(this.files).forEach(function (dosya, index) {

        const kutu = document.createElement("div");
        kutu.className = "fotograf-secim";

        if (index === 0) {
            kutu.classList.add("kapak");
        }

        const resim = document.createElement("img");
        resim.src = URL.createObjectURL(dosya);

        const yazi = document.createElement("div");
        yazi.className = "kapak-yazisi";
        yazi.textContent = index === 0 ? "✓ Kapak Fotoğrafı" : "Kapak Yap";

        kutu.appendChild(resim);
        kutu.appendChild(yazi);
        onizleme.appendChild(kutu);

        kutu.addEventListener("click", function () {

            document.querySelectorAll(".fotograf-secim").forEach(function (item) {
                item.classList.remove("kapak");
                item.querySelector(".kapak-yazisi").textContent = "Kapak Yap";
            });

            kutu.classList.add("kapak");
            yazi.textContent = "✓ Kapak Fotoğrafı";
            kapakIndex.value = index;
        });
    });
});
</script>




</body>
</html>
