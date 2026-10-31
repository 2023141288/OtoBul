
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
                minlength="10"
                maxlength="100"
                required
            >
                <small id="baslik-uyari" style="display:block; color:#777; margin-top:5px;">
                 En az 10, en fazla 100 karakter yazabilirsiniz.
                </small>

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
                min="1970"
                max="2050"
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
            <select id="il" name="il" required>
            <option value="">İl seçiniz</option>
            </select>

            <label for="ilce">İlçe</label>
            <select id="ilce" name="ilce" required disabled>
            <option value="">Önce il seçiniz</option>
            </select>


        <label for="boya_degisen">Aracın Boya / Değişen Durumunu Yazınız</label>
<textarea
    id="boya_degisen"
    name="boya_degisen"
    rows="4"
    maxlength="500"
    placeholder="Aracınızın boyalı ve değişen parçalarını yazınız..."
    required
></textarea>
        <small style="display:block; color:#777; margin-top:5px;">
    En fazla 500 karakter yazabilirsiniz.
</small>


            <label for="aciklama">İlan Açıklaması</label>
            <textarea
                id="aciklama"
                name="aciklama"
                rows="5"
                maxlength="2000"
                placeholder="Aracınız hakkında bilgi veriniz..."
                 required
            ></textarea>
                
                    <small style="display:block; color:#777; margin-top:5px;">
            En fazla 2000 karakter yazabilirsiniz.
                </small>


            <label for="fotograflar">Araç Fotoğrafları</label>
            <input
                type="file"
                id="fotograflar"
                name="fotograflar[]"
                accept="image/*"
                multiple
                required
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const fotografInput = document.getElementById("fotograflar");
const onizleme = document.getElementById("fotograf-onizleme");
const kapakIndex = document.getElementById("kapak_index");

fotografInput.addEventListener("change", function () {
    if (this.files.length > 10) {
    Swal.fire({
        title: "Uyarı",
        text: "En fazla 10 fotoğraf yükleyebilirsiniz.",
        confirmButtonText: "Tamam",
        confirmButtonColor: "#1e6ba8",
        width: "350px"
    });

    this.value = "";
    onizleme.innerHTML = "";
    kapakIndex.value = 0;
    return;
}
    const buyukFotograf = Array.from(this.files).find(function(dosya) {
    return dosya.size > 10 * 1024 * 1024;
});
    
if (buyukFotograf) {
    alert("Her fotoğraf en fazla 10 MB olabilir.");
    this.value = "";
    onizleme.innerHTML = "";
    kapakIndex.value = 0;
    return;
}

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

            ilceSecimi.innerHTML = '<option value="">İlçe seçiniz</option>';
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
