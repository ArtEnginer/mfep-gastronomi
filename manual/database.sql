-- GastroSmart Surakarta - MySQL Initialization
-- Jalankan file ini di phpMyAdmin / MySQL client

CREATE DATABASE IF NOT EXISTS gastro_smart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gastro_smart;

CREATE TABLE IF NOT EXISTS kriteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(10) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    warna VARCHAR(20) NOT NULL,
    bobot DECIMAL(5,4) NOT NULL,
    definisi TEXT NOT NULL,
    ukur TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS destinasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(10) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    jenis VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    lat DECIMAL(10,6) NOT NULL,
    lng DECIMAL(10,6) NOT NULL,
    jam VARCHAR(100) NOT NULL,
    K1 TINYINT NOT NULL,
    K2 TINYINT NOT NULL,
    K3 TINYINT NOT NULL,
    K4 TINYINT NOT NULL,
    K5 TINYINT NOT NULL,
    K6 TINYINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO kriteria (kode, nama, icon, warna, bobot, definisi, ukur)
VALUES
('K1','Keaslian Kuliner','utensils','#F59E0B',0.2500,'Tingkat orisinalitas resep, bahan baku, dan cara penyajian kuliner tradisional','Observasi + penilaian pakar kuliner (1=modern, 5=sangat autentik)'),
('K2','Nilai Budaya & Sejarah','landmark','#8B5CF6',0.2000,'Kekuatan narasi sejarah, identitas budaya Jawa, dan warisan leluhur pada kuliner','Studi dokumen + wawancara komunitas (1=rendah, 5=sangat tinggi)'),
('K3','Aksesibilitas Destinasi','map-pin','#10B981',0.1500,'Kemudahan jangkauan lokasi: transportasi, jarak, parkir, jam operasional','Data lapangan + rating aksesibilitas (1=sangat sulit, 5=sangat mudah)'),
('K4','Daya Tarik Wisata','star','#EF4444',0.2000,'Pengalaman wisata: ambiance, variasi menu, presentasi, dan ulasan pengunjung','Review platform digital + observasi (1=rendah, 5=sangat tinggi)'),
('K5','Dukungan Komunitas','users','#3B82F6',0.1000,'Keterlibatan komunitas lokal, pelaku UMKM, dan upaya kolektif pelestarian','FGD komunitas + kuesioner stakeholder (1=minim, 5=sangat aktif)'),
('K6','Potensi Ekonomi Lokal','trending-up','#06B6D4',0.1000,'Kontribusi destinasi pada pendapatan masyarakat lokal dan ekosistem pariwisata','Data ekonomi dinas + estimasi transaksi (1=rendah, 5=sangat besar)')
ON DUPLICATE KEY UPDATE
nama=VALUES(nama), icon=VALUES(icon), warna=VALUES(warna), bobot=VALUES(bobot), definisi=VALUES(definisi), ukur=VALUES(ukur);

INSERT INTO destinasi (kode, nama, jenis, deskripsi, lokasi, lat, lng, jam, K1, K2, K3, K4, K5, K6)
VALUES
('D01','Timlo Sastro','Timlo / Soto Tradisional','Warung ikonik di Pasar Gede dengan resep timlo yang tidak berubah sejak 1952. Kuah bening khas dengan babat dan telur pindang.','Pasar Gede, Jl. Urip Sumoharjo, Surakarta',-7.568600,110.832200,'06.00 - 13.00 WIB',5,5,5,5,4,4),
('D02','Nasi Liwet Bu Wongso Lemu','Nasi Liwet Tradisional','Legenda nasi liwet Solo yang berdiri sejak tahun 1950-an. Nasi gurih dengan lauk ayam, areh, dan sambal goreng yang autentik.','Jl. Teuku Umar, Keprabon, Surakarta',-7.562100,110.827100,'06.00 - 14.00 WIB',5,5,4,5,4,4),
('D03','Selat Solo Mbak Lies','Selat Solo (Bistik Jawa)','Hidangan fusion Belanda-Jawa warisan zaman kolonial. Daging sapi dengan mayones dan acar sayuran yang menjadi kekhasan Solo.','Jl. Brigjen Katamso, Surakarta',-7.566200,110.830100,'09.00 - 17.00 WIB',5,5,4,4,4,4),
('D04','Soto Gading','Soto Ayam Tradisional','Soto ayam bening khas Solo dengan bihun dan taburan seledri. Telah melayani pelanggan setia selama lebih dari empat dekade.','Jl. Brigjen Sudiarto, Surakarta',-7.570100,110.828900,'07.00 - 14.00 WIB',4,4,5,4,3,4),
('D05','Wedangan Jadul Pak Gareng','Wedangan / Angkringan','Angkringan bergaya retro dengan menu wedang jahe, kopi joss, dan berbagai lauk murah. Pusat interaksi sosial masyarakat Solo.','Jl. Slamet Riyadi, Surakarta',-7.569500,110.818000,'16.00 - 24.00 WIB',4,4,4,4,5,3),
('D06','Pasar Gede - Zona Kuliner','Kuliner Pasar Tradisional','Jantung kuliner Surakarta di pasar bersejarah era kolonial. Ratusan lapak kuliner tradisional dari timlo, intip, hingga dawet.','Jl. Jendral Urip Sumoharjo, Surakarta',-7.568300,110.832600,'05.00 - 17.00 WIB',4,5,5,4,5,5),
('D07','Nasi Gudeg Bu Semi','Gudeg Surakarta','Gudeg Solo versi kering dengan cita rasa lebih gurih dan manis dibanding gudeg Yogya. Sajian sarapan pagi yang ikonik.','Jl. Monginsidi, Surakarta',-7.572000,110.825500,'05.30 - 10.00 WIB',4,4,4,4,3,3),
('D08','Dawet Telasih Bu Dermi','Minuman Tradisional Dawet','Dawet legendaris di Pasar Gede dengan cendol dari tepung beras asli dan santan segar. Resep turun-temurun tiga generasi.','Pasar Gede, Surakarta',-7.568500,110.832800,'06.00 - 13.00 WIB',5,5,4,4,5,3),
('D09','Intip Goreng Pak Slamet','Camilan Tradisional Intip','Kerak nasi yang digoreng garing, camilan khas Solo yang semakin langka. Dibuat secara tradisional tanpa pengawet.','Pasar Klewer, Surakarta',-7.574500,110.826200,'08.00 - 16.00 WIB',5,4,3,3,4,3),
('D10','Sate Buntel Mbok Galak','Sate Kambing Khas Solo','Sate daging kambing cincang dibungkus lemak dengan bumbu kacang khas Solo. Salah satu kuliner heritage yang harus dicoba.','Jl. Sutan Syahrir, Surakarta',-7.568000,110.835000,'10.00 - 21.00 WIB',4,4,5,4,3,4),
('D11','Tengkleng Bu Edi','Tengkleng Kambing Tradisional','Sup tulang kambing dengan kuah bening kaya rempah. Resep 1960-an yang masih dipertahankan keasliannya hingga kini.','Pasar Klewer, Surakarta',-7.574300,110.825800,'11.00 - 16.00 WIB',5,5,4,5,4,4),
('D12','Cabuk Rambak Pak Birin','Cabuk Rambak Tradisional','Ketupat siraman bumbu wijen dengan kerupuk karak. Kuliner sarapan langka yang hampir punah di generasi muda.','Jl. Kyai Mojo, Surakarta',-7.563000,110.830000,'06.00 - 10.00 WIB',5,5,3,3,4,3),
('D13','Roti Mandarin Orion','Kue/Roti Tradisional Peranakan','Toko kue legendaris sejak 1945 dengan produk berbasis resep Tionghoa-Jawa. Menjadi ikon kuliner peranakan Solo.','Jl. Urip Sumoharjo, Surakarta',-7.567200,110.829000,'08.00 - 20.00 WIB',3,4,5,4,3,4),
('D14','Wedang Ronde Pak Min','Minuman Hangat Tradisional','Minuman hangat dengan bola-bola ketan berisi kacang dan jahe segar. Cocok dinikmati di malam hari.','Jl. Diponegoro, Surakarta',-7.565000,110.824000,'17.00 - 22.00 WIB',4,4,4,4,4,3),
('D15','Jenang Mirah','Jenang / Bubur Tradisional','Produsen jenang tradisional dengan berbagai varian seperti jenang sumsum, beras, dan ketan hitam yang dibuat tanpa pengawet.','Jl. Honggowongso, Surakarta',-7.578000,110.820000,'07.00 - 18.00 WIB',5,5,3,3,5,3),
('D16','Gempol Pleret Bu Sari','Minuman Tradisional Khas Solo','Minuman bola-bola tepung beras dalam santan manis, salah satu kuliner tradisional Solo yang paling autentik.','Pasar Gede, Surakarta',-7.568400,110.832700,'06.00 - 12.00 WIB',5,5,3,4,4,3),
('D17','Sego Pecel Bu Tun','Nasi Pecel Tradisional','Nasi pecel dengan sambal kacang halus dan beragam sayuran rebus. Sarapan rakyat Solo yang penuh nilai tradisi.','Jl. Kalilarangan, Surakarta',-7.576000,110.822000,'06.00 - 11.00 WIB',4,4,4,4,3,3),
('D18','Garang Asem Bu Sulastri','Garang Asem Ayam Tradisional','Ayam masak kuah asam-pedas dengan belimbing wuluh, dimasak dalam daun pisang. Kuliner khas Solo yang semakin langka.','Jl. Sutan Syahrir, Surakarta',-7.568200,110.834500,'10.00 - 16.00 WIB',5,5,3,4,4,3),
('D19','Bestik Jawa Pak Maryanto','Bestik Jawa (Bistik Tradisional)','Versi lokal bistik Belanda dengan daging sapi dan saus coklat khas Jawa. Warisan kuliner zaman kolonial yang tetap autentik.','Jl. Pasar Kliwon, Surakarta',-7.578000,110.833000,'10.00 - 20.00 WIB',5,5,4,4,4,4),
('D20','Pasar Triwindu - Kuliner Heritage','Kuliner Heritage & Antik','Kawasan pasar antik dengan spot kuliner tradisional yang terintegrasi dengan wisata budaya. Pengalaman gastronomi unik dan autentik.','Jl. Diponegoro, Surakarta',-7.564200,110.825300,'08.00 - 17.00 WIB',4,5,4,5,5,5)
ON DUPLICATE KEY UPDATE
nama=VALUES(nama), jenis=VALUES(jenis), deskripsi=VALUES(deskripsi), lokasi=VALUES(lokasi), lat=VALUES(lat), lng=VALUES(lng), jam=VALUES(jam),
K1=VALUES(K1), K2=VALUES(K2), K3=VALUES(K3), K4=VALUES(K4), K5=VALUES(K5), K6=VALUES(K6);

INSERT INTO admin_users (username, display_name, password_hash, is_active)
VALUES
('admin', 'Administrator', '$2y$10$n/cZKGCUJCRPXswuHtGc8uvmvy9HVFUVHag/XOwR4wx3HPXRog86W', 1)
ON DUPLICATE KEY UPDATE
password_hash=VALUES(password_hash), display_name=VALUES(display_name), is_active=VALUES(is_active);
