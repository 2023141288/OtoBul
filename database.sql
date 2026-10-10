CREATE DATABASE IF NOT EXISTS otobul
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE otobul;

CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
    rol ENUM('kullanici', 'admin') NOT NULL DEFAULT 'kullanici',
    kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ilanlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    baslik VARCHAR(150) NOT NULL,
    marka VARCHAR(50) NOT NULL,
    model VARCHAR(80) NOT NULL,
    motor VARCHAR(50) NOT NULL,
    yil INT NOT NULL,
    kilometre INT NOT NULL,
    yakit VARCHAR(30) NOT NULL,
    vites VARCHAR(30) NOT NULL,
    fiyat DECIMAL(12,2) NOT NULL,
    il VARCHAR(50) NOT NULL,
    ilce VARCHAR(80) NOT NULL,
    boya_degisen TEXT NOT NULL,
    aciklama TEXT,
    durum ENUM('beklemede', 'onaylandi', 'reddedildi') NOT NULL DEFAULT 'beklemede',
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ilan_kullanici
        FOREIGN KEY (kullanici_id)
        REFERENCES kullanicilar(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ilan_fotograflari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ilan_id INT NOT NULL,
    dosya_yolu VARCHAR(255) NOT NULL,
    kapak_mi TINYINT NOT NULL DEFAULT 0,

    CONSTRAINT fk_fotograf_ilan
        FOREIGN KEY (ilan_id)
        REFERENCES ilanlar(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favoriler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    ilan_id INT NOT NULL,

    UNIQUE (kullanici_id, ilan_id),

    FOREIGN KEY (kullanici_id)
        REFERENCES kullanicilar(id)
        ON DELETE CASCADE,

    FOREIGN KEY (ilan_id)
        REFERENCES ilanlar(id)
        ON DELETE CASCADE
);

-- Kullanıcılar arasındaki mesajlar
CREATE TABLE IF NOT EXISTS mesajlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ilan_id INT NOT NULL,
    gonderen_id INT NOT NULL,
    alici_id INT NOT NULL,
    mesaj TEXT NOT NULL,
    gonderim_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (ilan_id)
        REFERENCES ilanlar(id)
        ON DELETE CASCADE,

    FOREIGN KEY (gonderen_id)
        REFERENCES kullanicilar(id)
        ON DELETE CASCADE,

    FOREIGN KEY (alici_id)
        REFERENCES kullanicilar(id)
        ON DELETE CASCADE
);

-- Kullanıcıların kendi hesaplarında gizlediği konuşmalar
CREATE TABLE IF NOT EXISTS gizlenen_konusmalar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    ilan_id INT NOT NULL,
    diger_kullanici_id INT NOT NULL,
    son_gizlenen_mesaj_id INT NOT NULL,

    UNIQUE (kullanici_id, ilan_id, diger_kullanici_id),

    FOREIGN KEY (kullanici_id)
        REFERENCES kullanicilar(id)
        ON DELETE CASCADE,

    FOREIGN KEY (ilan_id)
        REFERENCES ilanlar(id)
        ON DELETE CASCADE,

    FOREIGN KEY (diger_kullanici_id)
        REFERENCES kullanicilar(id)
        ON DELETE CASCADE
);
