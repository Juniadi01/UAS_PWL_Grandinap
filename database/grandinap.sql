-- ============================================================
--  GrandInap - Skema Database (MySQL)
--  Import file ini via phpMyAdmin atau:  mysql -u root -p < grandinap.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS grandinap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE grandinap;

-- Hapus tabel lama (urutan aman terhadap foreign key: anak dulu, induk belakangan)
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS wishlists;
DROP TABLE IF EXISTS room_facility;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS facilities;
DROP TABLE IF EXISTS hotels;
DROP TABLE IF EXISTS users;

-- ----------------------------------------------------------
-- USERS : admin & pelanggan (Autentikasi + Otorisasi peran)
-- ----------------------------------------------------------
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(120) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,         -- disimpan sebagai hash bcrypt
    role       ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    phone      VARCHAR(20)  DEFAULT NULL,
    photo      VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- HOTELS : daftar hotel bintang 5 (premium)
-- ----------------------------------------------------------
CREATE TABLE hotels (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    city        VARCHAR(100) NOT NULL,
    address     TEXT,
    star        TINYINT NOT NULL DEFAULT 5,
    description TEXT,
    photo       VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- FACILITIES : master fasilitas (wifi, kolam, dll)
-- ----------------------------------------------------------
CREATE TABLE facilities (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(80) NOT NULL,
    icon       VARCHAR(60) DEFAULT 'bi-check-circle', -- kelas ikon Bootstrap Icons
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- ROOMS : kamar milik sebuah hotel (relasi Hotel -> Kamar)
-- ----------------------------------------------------------
CREATE TABLE rooms (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id    INT NOT NULL,
    name        VARCHAR(120) NOT NULL,
    type        ENUM('Deluxe','Suite','Presidential Suite','Standard') NOT NULL DEFAULT 'Deluxe',
    price       DECIMAL(12,2) NOT NULL,
    capacity    INT NOT NULL DEFAULT 2,
    description TEXT,
    photo       VARCHAR(255) DEFAULT NULL,
    status      ENUM('available','maintenance') NOT NULL DEFAULT 'available',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- ROOM_FACILITY : pivot many-to-many kamar <-> fasilitas
-- ----------------------------------------------------------
CREATE TABLE room_facility (
    room_id     INT NOT NULL,
    facility_id INT NOT NULL,
    PRIMARY KEY (room_id, facility_id),
    FOREIGN KEY (room_id)     REFERENCES rooms(id)      ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- RESERVATIONS : pemesanan kamar oleh pelanggan
-- ----------------------------------------------------------
CREATE TABLE reservations (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    room_id     INT NOT NULL,
    check_in    DATE NOT NULL,
    check_out   DATE NOT NULL,
    guests      INT NOT NULL DEFAULT 1,
    nights      INT NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    status      ENUM('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- PAYMENTS : pembayaran (upload bukti -> verifikasi admin)
-- ----------------------------------------------------------
CREATE TABLE payments (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    amount         DECIMAL(12,2) NOT NULL,
    method         VARCHAR(60) DEFAULT 'Transfer Bank',
    proof          VARCHAR(255) DEFAULT NULL,  -- nama file bukti transfer
    status         ENUM('unpaid','waiting','verified','rejected') NOT NULL DEFAULT 'unpaid',
    note           VARCHAR(255) DEFAULT NULL,
    paid_at        TIMESTAMP NULL DEFAULT NULL,
    verified_at    TIMESTAMP NULL DEFAULT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  DATA AWAL (SEED)
-- ============================================================

-- Password: admin123 (admin) & customer123 (pelanggan)
INSERT INTO users (name, email, password, role, phone) VALUES
('Administrator', 'admin@grandinap.com', '$2b$10$4WpcTRKKCwQN9bY0djzK7ez/CrVgS383ODStvE4PHkf8DSyXv2HPK', 'admin', '081200000000'),
('Juniadi', 'juni@mail.com', '$2b$10$E14c8E0P4iEWZlP86Tqx/OybRLQBPnfoPK/Oz26aWhicpTGuUw1au', 'customer', '081311112222'),
('Shindy Novryanti Br Munthe', 'shindy@mail.com', '$2b$10$0mk35qJTrYBPZgEchSFXMendISE5IOZNvNHeO1Dcr3LO5dHyAmTbm', 'customer', '081320001001'),
('Rochimatul Habibah', 'rochim@mail.com', '$2b$10$f6tbbdKNUvr5aCwgVr7uJOHs9mxrqtvKBBQvICN/1LGEHjPGuKaJy', 'customer', '081320001002'),
('Guritno Kusumo Widagdo', 'guritno@mail.com', '$2b$10$LaGEEJsZSkPSWqp5FhY9r.Y4Z4Yl4.7C4bTv1ApYhYgZru/mWe5LK', 'customer', '081320001003'),
('Novalin Mametapare', 'novalin@mail.com', '$2b$10$ApiWM8alDy7jPFILfwZFVewKyMlLBlCwU6iuO.0jCz8XBGViFITX2', 'customer', '081320001004');

INSERT INTO hotels (name, city, address, star, description, photo) VALUES
('Aman Villas at Nusa Dua', 'Bali', 'Nusa Dua, Bali', 5, 'Retret villa ultra-privat Aman Group dengan dua butler dan koki pribadi, akses langsung ke Beach Terrace.', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=900&auto=format&fit=crop'),
('Four Seasons Resort Bali at Jimbaran Bay', 'Bali', 'Jimbaran, Bali', 5, 'Resor tepi pantai dengan villa berkolam pribadi dan pengalaman budaya Bali yang otentik.', 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?q=80&w=900&auto=format&fit=crop'),
('Bulgari Resort Bali', 'Bali', 'Uluwatu, Bali', 5, 'Desain Italia elegan di puncak tebing 160m Uluwatu dengan Serpenti Pool Club dan Bulgari Spa.', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop'),
('The St. Regis Bali Resort', 'Bali', 'Nusa Dua, Bali', 5, 'Barefoot Elegance dengan layanan butler 24 jam, kolam laguna, dan sarapan legendaris di Boneka.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop'),
('Mandapa, a Ritz-Carlton Reserve', 'Bali', 'Ubud, Bali', 5, 'Sanctuary tepi Sungai Ayung dengan butler pribadi (Patih) dan dining di pod bambu Kubu.', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=900&auto=format&fit=crop'),
('The Langham, Jakarta', 'Jakarta', 'SCBD, Jakarta', 5, 'Hotel ultra-mewah bernuansa klasik Inggris di SCBD, peraih Michelin Key 2025.', 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?q=80&w=900&auto=format&fit=crop'),
('Raffles Jakarta', 'Jakarta', 'Kuningan, Jakarta', 5, 'Hotel seni dengan Raffles Butler Service dan akses langsung ke Ciputra Artpreneur.', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=900&auto=format&fit=crop'),
('Park Hyatt Jakarta', 'Jakarta', 'Kebon Sirih, Jakarta', 5, 'Kemewahan modern di puncak MNC Center dengan pemandangan Monas terbaik.', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?q=80&w=900&auto=format&fit=crop'),
('Amanjiwo', 'Magelang', 'Borobudur, Magelang', 5, 'Resor rotunda ikonik menghadap Candi Borobudur dengan paviliun atap jerami.', 'https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=900&auto=format&fit=crop'),
('The Oberoi Beach Resort, Lombok', 'Lombok', 'Pantai Medana, Lombok', 5, 'Resor tenang di taman tropis 24 hektar dengan dermaga pribadi menuju Gili Islands.', 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=900&auto=format&fit=crop'),
('TA''AKTANA, a Luxury Collection Resort & Spa', 'Labuan Bajo', 'Pantai Labuan Bajo, Flores', 5, 'Resor terinspirasi sawah Lingko dengan Overwater Villa pertama di Labuan Bajo.', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop'),
('Nihi Sumba', 'Sumba', 'Pantai Nihiwatu, Sumba Barat', 5, 'Resor Edge of Wildness dengan Spa Safari Nihioka dan ombak surfing kelas dunia.', 'https://images.unsplash.com/photo-1455587734955-081b22074882?q=80&w=900&auto=format&fit=crop'),
('Plataran Komodo Resort & Spa', 'Labuan Bajo', 'Pantai Waecicu, Labuan Bajo', 5, 'Resor paling privat di Waecicu dengan armada kapal Phinisi pribadi.', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=900&auto=format&fit=crop'),
('The Phoenix Hotel Yogyakarta - MGallery', 'Yogyakarta', 'Jl. Jenderal Sudirman, Yogyakarta', 5, 'Hotel heritage kolonial 1918 bernuansa Jawa-Eropa dengan kolam ikonik di courtyard.', 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?q=80&w=900&auto=format&fit=crop'),
('The Gaia Hotel Bandung', 'Bandung', 'Jl. Dr. Setiabudi, Bandung', 5, 'Hotel berarsitektur perpustakaan raksasa dengan The Inspiration Pool yang Instagramable.', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop'),
('InterContinental Bandung Dago Pakar', 'Bandung', 'Dago Pakar, Bandung', 5, 'Hotel mewah Dago dengan infinity pool air hangat berpemandangan kota Bandung.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop'),
('JW Marriott Hotel Surabaya', 'Surabaya', 'Jl. Embong Malang, Surabaya', 5, 'Hotel klasik mewah dekat Tunjungan Plaza dengan Uppercut Steakhouse ternama.', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=900&auto=format&fit=crop'),
('Vasa Hotel Surabaya', 'Surabaya', 'Jl. HR Muhammad, Surabaya Barat', 5, 'Hotel modern dengan Heliport pribadi dan Chamas Brazilian Steakhouse.', 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?q=80&w=900&auto=format&fit=crop'),
('The Sanchaya, Bintan', 'Bintan', 'Lagoi, Pulau Bintan', 5, 'Estate kolonial tepi pantai eksklusif dengan layanan imigrasi ekspres pribadi.', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=900&auto=format&fit=crop'),
('Sheraton Belitung Resort', 'Belitung', 'Tanjung Binga, Belitung', 5, 'Resor eco-friendly dikelilingi hutan lindung dan pantai pasir putih ikonik Belitung.', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?q=80&w=900&auto=format&fit=crop'),
('The Rinra Makassar', 'Makassar', 'Jl. Metro Tanjung Bunga, Makassar', 5, 'Hotel lifestyle dengan infinity pool berpemandangan sunset Pantai Losari.', 'https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=900&auto=format&fit=crop'),
('The Apurva Kempinski Bali', 'Bali', 'Nusa Dua, Bali', 5, 'Resor berundak terinspirasi pura Bali dengan Koral, restoran bawah laut pertama di Bali.', 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=900&auto=format&fit=crop'),
('Capella Ubud, Bali', 'Bali', 'Keliki, Ubud, Bali', 5, 'Tented camp ultra-mewah karya Bill Bensley di hutan Ubud dengan kolam air asin pribadi.', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop'),
('Amanwana, Pulau Moyo', 'Pulau Moyo', 'Pulau Moyo, Nusa Tenggara Barat', 5, 'Satu-satunya resor di Pulau Moyo, wilderness hideaway dengan akses taman nasional laut.', 'https://images.unsplash.com/photo-1455587734955-081b22074882?q=80&w=900&auto=format&fit=crop'),
('Amankila, Bali', 'Bali', 'Manggis, Karangasem, Bali', 5, 'Resor legendaris Bali Timur dengan kolam renang tiga tingkat ikonik menghadap Selat Lombok.', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=900&auto=format&fit=crop');

INSERT INTO facilities (name, icon) VALUES
('WiFi Cepat', 'bi-wifi'),
('Kolam Renang', 'bi-water'),
('AC', 'bi-snow'),
('Sarapan', 'bi-cup-hot'),
('Smart TV', 'bi-tv'),
('Bathtub', 'bi-droplet'),
('Mini Bar', 'bi-cup-straw'),
('Gym', 'bi-bicycle'),
('Spa', 'bi-flower1'),
('Butler 24 Jam', 'bi-bell'),
('Kolam Renang Pribadi', 'bi-droplet-half'),
('Akses Pantai', 'bi-umbrella'),
('Fine Dining', 'bi-egg-fried'),
('Bar & Lounge', 'bi-cup'),
('Pemandangan Laut', 'bi-binoculars'),
('Pusat Selam', 'bi-life-preserver'),
('Antar-Jemput', 'bi-car-front'),
('Taman Tropis', 'bi-tree');

INSERT INTO rooms (hotel_id, name, type, price, capacity, description, photo, status) VALUES
(1, 'One Bedroom Villa', 'Suite', 40000000, 2, 'One Bedroom Villa di Aman Villas at Nusa Dua - Retret villa ultra-privat Aman Group dengan dua butler dan koki pribad', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop', 'available'),
(1, 'Aman Pool Villa', 'Presidential Suite', 72000000, 6, 'Aman Pool Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop', 'available'),
(2, 'Premier Ocean Villa', 'Suite', 15000000, 2, 'Premier Ocean Villa di Four Seasons Resort Bali at Jimbaran Bay - Resor tepi pantai dengan villa berkolam pribadi dan pengalaman budaya ', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=900&auto=format&fit=crop', 'available'),
(2, 'Imperial Three-Bedroom Villa', 'Presidential Suite', 27000000, 6, 'Imperial Three-Bedroom Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=900&auto=format&fit=crop', 'available'),
(3, 'Ocean View Villa', 'Suite', 25000000, 2, 'Ocean View Villa di Bulgari Resort Bali - Desain Italia elegan di puncak tebing 160m Uluwatu dengan Serpenti Poo', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop', 'available'),
(3, 'The Bulgari Villa', 'Presidential Suite', 45000000, 4, 'The Bulgari Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=900&auto=format&fit=crop', 'available'),
(4, 'St. Regis Suite', 'Suite', 12000000, 2, 'St. Regis Suite di The St. Regis Bali Resort - Barefoot Elegance dengan layanan butler 24 jam, kolam laguna, dan sara', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=900&auto=format&fit=crop', 'available'),
(4, 'Strand Villa', 'Presidential Suite', 21600000, 4, 'Strand Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=900&auto=format&fit=crop', 'available'),
(5, 'Reserve Suite', 'Suite', 24000000, 2, 'Reserve Suite di Mandapa, a Ritz-Carlton Reserve - Sanctuary tepi Sungai Ayung dengan butler pribadi (Patih) dan dining d', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop', 'available'),
(5, 'Pool Villa', 'Presidential Suite', 43200000, 4, 'Pool Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop', 'available'),
(6, 'Deluxe Cityscape', 'Deluxe', 4500000, 2, 'Deluxe Cityscape di The Langham, Jakarta - Hotel ultra-mewah bernuansa klasik Inggris di SCBD, peraih Michelin Ke', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=900&auto=format&fit=crop', 'available'),
(6, 'One Bedroom Suite', 'Suite', 8100000, 3, 'One Bedroom Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=900&auto=format&fit=crop', 'available'),
(7, 'Raffles Room', 'Deluxe', 4000000, 2, 'Raffles Room di Raffles Jakarta - Hotel seni dengan Raffles Butler Service dan akses langsung ke Ciputra', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop', 'available'),
(7, 'Raffles Suite', 'Suite', 7200000, 3, 'Raffles Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=900&auto=format&fit=crop', 'available'),
(8, 'Park Room', 'Deluxe', 4500000, 2, 'Park Room di Park Hyatt Jakarta - Kemewahan modern di puncak MNC Center dengan pemandangan Monas terbaik', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=900&auto=format&fit=crop', 'available'),
(8, 'Park Suite', 'Suite', 8100000, 3, 'Park Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=900&auto=format&fit=crop', 'available'),
(9, 'Garden Suite', 'Suite', 20000000, 2, 'Garden Suite di Amanjiwo - Resor rotunda ikonik menghadap Candi Borobudur dengan paviliun atap je', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop', 'available'),
(9, 'Pool Suite', 'Presidential Suite', 36000000, 4, 'Pool Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop', 'available'),
(10, 'Luxury Pavilion', 'Deluxe', 6000000, 2, 'Luxury Pavilion di The Oberoi Beach Resort, Lombok - Resor tenang di taman tropis 24 hektar dengan dermaga pribadi menuju G', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=900&auto=format&fit=crop', 'available'),
(10, 'Luxury Villa Pool', 'Suite', 10800000, 3, 'Luxury Villa Pool - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=900&auto=format&fit=crop', 'available'),
(11, 'Suite', 'Suite', 9000000, 2, 'Suite di TA''AKTANA, a Luxury Collection Resort & Spa - Resor terinspirasi sawah Lingko dengan Overwater Villa pertama di Labu', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop', 'available'),
(11, 'Overwater Villa', 'Presidential Suite', 16200000, 4, 'Overwater Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=900&auto=format&fit=crop', 'available'),
(12, 'Marangga Villa', 'Suite', 18000000, 2, 'Marangga Villa di Nihi Sumba - Resor Edge of Wildness dengan Spa Safari Nihioka dan ombak surfing kel', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=900&auto=format&fit=crop', 'available'),
(12, 'Raja Mandaka Villa', 'Presidential Suite', 32400000, 6, 'Raja Mandaka Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=900&auto=format&fit=crop', 'available'),
(13, 'Deluxe Beach Front Villa', 'Suite', 7000000, 2, 'Deluxe Beach Front Villa di Plataran Komodo Resort & Spa - Resor paling privat di Waecicu dengan armada kapal Phinisi pribadi.', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop', 'available'),
(13, 'Grand Pool Villa', 'Presidential Suite', 12600000, 4, 'Grand Pool Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop', 'available'),
(14, 'Superior Room', 'Deluxe', 1500000, 2, 'Superior Room di The Phoenix Hotel Yogyakarta - MGallery - Hotel heritage kolonial 1918 bernuansa Jawa-Eropa dengan kolam ikonik ', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=900&auto=format&fit=crop', 'available'),
(14, 'Phoenix Suite', 'Suite', 2700000, 3, 'Phoenix Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=900&auto=format&fit=crop', 'available'),
(15, 'Deluxe Room', 'Deluxe', 2500000, 2, 'Deluxe Room di The Gaia Hotel Bandung - Hotel berarsitektur perpustakaan raksasa dengan The Inspiration Pool y', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop', 'available'),
(15, 'The Gaia Suite', 'Suite', 4500000, 4, 'The Gaia Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=900&auto=format&fit=crop', 'available'),
(16, 'Classic Room', 'Deluxe', 2000000, 2, 'Classic Room di InterContinental Bandung Dago Pakar - Hotel mewah Dago dengan infinity pool air hangat berpemandangan kota B', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=900&auto=format&fit=crop', 'available'),
(16, 'Kencana Villa', 'Suite', 3600000, 4, 'Kencana Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=900&auto=format&fit=crop', 'available'),
(17, 'Deluxe Room', 'Deluxe', 1800000, 2, 'Deluxe Room di JW Marriott Hotel Surabaya - Hotel klasik mewah dekat Tunjungan Plaza dengan Uppercut Steakhouse te', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop', 'available'),
(17, 'Junior Suite', 'Suite', 3200000, 3, 'Junior Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop', 'available'),
(18, 'Select Room', 'Deluxe', 1500000, 2, 'Select Room di Vasa Hotel Surabaya - Hotel modern dengan Heliport pribadi dan Chamas Brazilian Steakhouse.', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=900&auto=format&fit=crop', 'available'),
(18, 'Suite', 'Suite', 2700000, 3, 'Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=900&auto=format&fit=crop', 'available'),
(19, 'Junior Suite', 'Suite', 10000000, 2, 'Junior Suite di The Sanchaya, Bintan - Estate kolonial tepi pantai eksklusif dengan layanan imigrasi ekspres ', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop', 'available'),
(19, 'Vanda Villa', 'Presidential Suite', 18000000, 4, 'Vanda Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=900&auto=format&fit=crop', 'available'),
(20, 'Deluxe Room', 'Deluxe', 1500000, 2, 'Deluxe Room di Sheraton Belitung Resort - Resor eco-friendly dikelilingi hutan lindung dan pantai pasir putih ik', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=900&auto=format&fit=crop', 'available'),
(20, 'Terrace Villa', 'Suite', 2700000, 4, 'Terrace Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=900&auto=format&fit=crop', 'available'),
(21, 'Deluxe Room', 'Deluxe', 1200000, 2, 'Deluxe Room di The Rinra Makassar - Hotel lifestyle dengan infinity pool berpemandangan sunset Pantai Losa', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop', 'available'),
(21, 'Rinra Suite', 'Suite', 2200000, 3, 'Rinra Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop', 'available'),
(22, 'Grand Deluxe', 'Deluxe', 5000000, 2, 'Grand Deluxe di The Apurva Kempinski Bali - Resor berundak terinspirasi pura Bali dengan Koral, restoran bawah lau', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=900&auto=format&fit=crop', 'available'),
(22, 'Ocean Front Private Pool Villa', 'Presidential Suite', 9000000, 4, 'Ocean Front Private Pool Villa - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=900&auto=format&fit=crop', 'available'),
(23, 'Rainforest Tent', 'Suite', 15000000, 2, 'Rainforest Tent di Capella Ubud, Bali - Tented camp ultra-mewah karya Bill Bensley di hutan Ubud dengan kolam ', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=900&auto=format&fit=crop', 'available'),
(23, 'The Lodge Two-Bedroom', 'Presidential Suite', 27000000, 4, 'The Lodge Two-Bedroom - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=900&auto=format&fit=crop', 'available'),
(24, 'Jungle Tent', 'Suite', 15000000, 2, 'Jungle Tent di Amanwana, Pulau Moyo - Satu-satunya resor di Pulau Moyo, wilderness hideaway dengan akses tam', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=900&auto=format&fit=crop', 'available'),
(24, 'Ocean Tent', 'Suite', 27000000, 2, 'Ocean Tent - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=900&auto=format&fit=crop', 'available'),
(25, 'Garden Suite', 'Suite', 18000000, 2, 'Garden Suite di Amankila, Bali - Resor legendaris Bali Timur dengan kolam renang tiga tingkat ikonik me', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=900&auto=format&fit=crop', 'available'),
(25, 'Amankila Suite', 'Presidential Suite', 32400000, 4, 'Amankila Suite - akomodasi premium dengan ruang lebih luas dan fasilitas eksklusif.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=900&auto=format&fit=crop', 'available');

-- Pasang fasilitas ke beberapa kamar
INSERT INTO room_facility (room_id, facility_id) VALUES
(1,1),(1,3),(1,4),(1,5),(1,6),(1,7),
(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,9),(2,10),(2,11),(2,12),(2,15),
(3,1),(3,3),(3,4),(3,5),(3,6),(3,7),
(4,1),(4,2),(4,3),(4,4),(4,5),(4,6),(4,7),(4,9),(4,10),(4,11),(4,12),(4,15),
(5,1),(5,3),(5,4),(5,5),(5,6),(5,7),
(6,1),(6,2),(6,3),(6,4),(6,5),(6,6),(6,7),(6,9),(6,10),(6,11),(6,12),(6,15),
(7,1),(7,3),(7,4),(7,5),(7,6),(7,7),
(8,1),(8,2),(8,3),(8,4),(8,5),(8,6),(8,7),(8,9),(8,10),(8,11),(8,12),(8,15),
(9,1),(9,3),(9,4),(9,5),(9,6),(9,7),
(10,1),(10,2),(10,3),(10,4),(10,5),(10,6),(10,7),(10,9),(10,10),(10,11),(10,12),(10,15),
(11,1),(11,3),(11,4),(11,5),(11,6),(11,7),
(12,1),(12,2),(12,3),(12,4),(12,5),(12,6),(12,7),(12,9),(12,10),(12,11),(12,12),(12,15),
(13,1),(13,3),(13,4),(13,5),(13,6),(13,7),
(14,1),(14,2),(14,3),(14,4),(14,5),(14,6),(14,7),(14,9),(14,10),(14,11),(14,12),(14,15),
(15,1),(15,3),(15,4),(15,5),(15,6),(15,7),
(16,1),(16,2),(16,3),(16,4),(16,5),(16,6),(16,7),(16,9),(16,10),(16,11),(16,12),(16,15),
(17,1),(17,3),(17,4),(17,5),(17,6),(17,7),
(18,1),(18,2),(18,3),(18,4),(18,5),(18,6),(18,7),(18,9),(18,10),(18,11),(18,12),(18,15),
(19,1),(19,3),(19,4),(19,5),(19,6),(19,7),
(20,1),(20,2),(20,3),(20,4),(20,5),(20,6),(20,7),(20,9),(20,10),(20,11),(20,12),(20,15),
(21,1),(21,3),(21,4),(21,5),(21,6),(21,7),
(22,1),(22,2),(22,3),(22,4),(22,5),(22,6),(22,7),(22,9),(22,10),(22,11),(22,12),(22,15),
(23,1),(23,3),(23,4),(23,5),(23,6),(23,7),
(24,1),(24,2),(24,3),(24,4),(24,5),(24,6),(24,7),(24,9),(24,10),(24,11),(24,12),(24,15),
(25,1),(25,3),(25,4),(25,5),(25,6),(25,7),
(26,1),(26,2),(26,3),(26,4),(26,5),(26,6),(26,7),(26,9),(26,10),(26,11),(26,12),(26,15),
(27,1),(27,3),(27,4),(27,5),(27,6),(27,7),
(28,1),(28,2),(28,3),(28,4),(28,5),(28,6),(28,7),(28,9),(28,10),(28,11),(28,12),(28,15),
(29,1),(29,3),(29,4),(29,5),(29,6),(29,7),
(30,1),(30,2),(30,3),(30,4),(30,5),(30,6),(30,7),(30,9),(30,10),(30,11),(30,12),(30,15),
(31,1),(31,3),(31,4),(31,5),(31,6),(31,7),
(32,1),(32,2),(32,3),(32,4),(32,5),(32,6),(32,7),(32,9),(32,10),(32,11),(32,12),(32,15),
(33,1),(33,3),(33,4),(33,5),(33,6),(33,7),
(34,1),(34,2),(34,3),(34,4),(34,5),(34,6),(34,7),(34,9),(34,10),(34,11),(34,12),(34,15),
(35,1),(35,3),(35,4),(35,5),(35,6),(35,7),
(36,1),(36,2),(36,3),(36,4),(36,5),(36,6),(36,7),(36,9),(36,10),(36,11),(36,12),(36,15),
(37,1),(37,3),(37,4),(37,5),(37,6),(37,7),
(38,1),(38,2),(38,3),(38,4),(38,5),(38,6),(38,7),(38,9),(38,10),(38,11),(38,12),(38,15),
(39,1),(39,3),(39,4),(39,5),(39,6),(39,7),
(40,1),(40,2),(40,3),(40,4),(40,5),(40,6),(40,7),(40,9),(40,10),(40,11),(40,12),(40,15),
(41,1),(41,3),(41,4),(41,5),(41,6),(41,7),
(42,1),(42,2),(42,3),(42,4),(42,5),(42,6),(42,7),(42,9),(42,10),(42,11),(42,12),(42,15),
(43,1),(43,3),(43,4),(43,5),(43,6),(43,7),
(44,1),(44,2),(44,3),(44,4),(44,5),(44,6),(44,7),(44,9),(44,10),(44,11),(44,12),(44,15),
(45,1),(45,3),(45,4),(45,5),(45,6),(45,7),
(46,1),(46,2),(46,3),(46,4),(46,5),(46,6),(46,7),(46,9),(46,10),(46,11),(46,12),(46,15),
(47,1),(47,3),(47,4),(47,5),(47,6),(47,7),
(48,1),(48,2),(48,3),(48,4),(48,5),(48,6),(48,7),(48,9),(48,10),(48,11),(48,12),(48,15),
(49,1),(49,3),(49,4),(49,5),(49,6),(49,7),
(50,1),(50,2),(50,3),(50,4),(50,5),(50,6),(50,7),(50,9),(50,10),(50,11),(50,12),(50,15);

-- Contoh reservasi + pembayaran agar laporan/grafik langsung ada isinya
INSERT INTO reservations (user_id, room_id, check_in, check_out, guests, nights, total_price, status) VALUES
(2, 1, '2026-01-10', '2026-01-12', 2, 2, 80000000, 'checked_out'),
(2, 3, '2026-02-08', '2026-02-10', 2, 2, 30000000, 'checked_out'),
(2, 5, '2026-03-05', '2026-03-07', 2, 2, 50000000, 'checked_out'),
(2, 7, '2026-03-20', '2026-03-22', 2, 2, 24000000, 'checked_out'),
(2, 9, '2026-04-12', '2026-04-14', 2, 2, 48000000, 'checked_out'),
(2, 11, '2026-05-02', '2026-05-04', 2, 2, 9000000, 'checked_out'),
(2, 27, '2026-05-18', '2026-05-20', 2, 2, 3000000, 'checked_out'),
(2, 29, '2026-06-03', '2026-06-05', 2, 2, 5000000, 'checked_out');

INSERT INTO payments (reservation_id, amount, method, status, paid_at, verified_at) VALUES
(1, 80000000, 'Transfer Bank', 'verified', NOW(), NOW()),
(2, 30000000, 'Transfer Bank', 'verified', NOW(), NOW()),
(3, 50000000, 'Transfer Bank', 'verified', NOW(), NOW()),
(4, 24000000, 'Transfer Bank', 'verified', NOW(), NOW()),
(5, 48000000, 'Transfer Bank', 'verified', NOW(), NOW()),
(6, 9000000, 'Transfer Bank', 'verified', NOW(), NOW()),
(7, 3000000, 'Transfer Bank', 'verified', NOW(), NOW()),
(8, 5000000, 'Transfer Bank', 'verified', NOW(), NOW());

-- ============================================================
--  TABEL FITUR TAMBAHAN (v2): Ulasan & Favorit
-- ============================================================

-- ULASAN/RATING : pelanggan menilai kamar setelah menginap (checked_out)
CREATE TABLE reviews (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL UNIQUE,            -- 1 ulasan per reservasi
    user_id        INT NOT NULL,
    room_id        INT NOT NULL,
    rating         TINYINT NOT NULL,               -- 1..5
    comment        TEXT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)        REFERENCES users(id)        ON DELETE CASCADE,
    FOREIGN KEY (room_id)        REFERENCES rooms(id)        ON DELETE CASCADE
) ENGINE=InnoDB;

-- WISHLIST/FAVORIT : kamar yang disimpan pelanggan
CREATE TABLE wishlists (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    room_id    INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_wish (user_id, room_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed: 1 ulasan untuk kamar 1, dan 1 favorit
INSERT INTO reviews (reservation_id, user_id, room_id, rating, comment) VALUES
(1, 2, 1, 5, 'Pengalaman menginap terbaik! Villa sangat privat, butler ramah, dan pemandangannya luar biasa.'),
(2, 2, 3, 5, 'Resor tepi pantai yang menakjubkan. Sarapannya enak dan stafnya sangat membantu.'),
(3, 2, 5, 5, 'Desain mewah dan lokasi tebing yang ikonik. Sangat worth it untuk momen spesial.'),
(4, 2, 7, 4, 'Pelayanan butler sangat personal. Kamar bersih dan nyaman, hanya akses agak jauh.'),
(5, 2, 9, 5, 'Suasana tepi sungai yang menenangkan. Dining di pod bambu pengalaman tak terlupakan.'),
(6, 2, 11, 5, 'Lokasi strategis di pusat kota, restoran kelas dunia. Cocok untuk perjalanan bisnis.'),
(7, 2, 27, 4, 'Hotel heritage yang penuh karakter, kolam courtyard-nya cantik sekali.'),
(8, 2, 29, 5, 'Arsitektur perpustakaannya keren banget dan sangat Instagramable. Anak-anak suka kids club-nya.');

INSERT INTO wishlists (user_id, room_id) VALUES
(2, 2),
(2, 5),
(2, 12),
(2, 24);
