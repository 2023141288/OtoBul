CREATE DATABASE IF NOT EXISTS otobul
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE otobul;

CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
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