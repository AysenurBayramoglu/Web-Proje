<?php
session_start();
include("baglanti.php");

// Hata raporlamayı aktif et
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantı kontrolü
if (!$baglanti) {
    error_log("Veritabanı bağlantı hatası: " . mysqli_connect_error());
    echo json_encode(['hata' => 'Veritabanı bağlantı hatası']);
    exit;
}

// JSON verisini al
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    error_log("Geçersiz JSON verisi: " . file_get_contents('php://input'));
    echo json_encode(['hata' => 'Geçersiz veri']);
    exit;
}

$etkinlik_id = $data['etkinlik_id'];
$bilet_turu = $data['bilet_turu'];

// Kullanıcı emailini session'dan al
$kullanici_email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
if (!$kullanici_email) {
    echo json_encode(['hata' => 'Kullanıcı oturumu bulunamadı. Lütfen tekrar giriş yapın.']);
    exit;
}

// Önce kontenjan kontrolü yap
$kontenjan_sorgu = "SELECT kontenjan FROM etkinlikler WHERE id = ?";
$stmt = $baglanti->prepare($kontenjan_sorgu);
if (!$stmt) {
    error_log("Kontenjan sorgusu hazırlama hatası: " . $baglanti->error);
    echo json_encode(['hata' => 'Sistem hatası']);
    exit;
}

$stmt->bind_param("i", $etkinlik_id);
if (!$stmt->execute()) {
    error_log("Kontenjan sorgusu çalıştırma hatası: " . $stmt->error);
    echo json_encode(['hata' => 'Sistem hatası']);
    exit;
}

$sonuc = $stmt->get_result();
$etkinlik = $sonuc->fetch_assoc();

if (!$etkinlik) {
    error_log("Etkinlik bulunamadı: ID=" . $etkinlik_id);
    echo json_encode(['hata' => 'Etkinlik bulunamadı']);
    exit;
}

if ($etkinlik['kontenjan'] <= 0) {
    echo json_encode(['hata' => 'Üzgünüz, bu etkinlik için kontenjan dolmuştur.']);
    exit;
}

// Fiyat ve etkinlik adını al
$fiyat_sorgu = "SELECT fiyat_normal, fiyat_ogrenci, ad FROM etkinlikler WHERE id = ?";
$stmt = $baglanti->prepare($fiyat_sorgu);
if (!$stmt) {
    error_log("Fiyat sorgusu hazırlama hatası: " . $baglanti->error);
    echo json_encode(['hata' => 'Sistem hatası']);
    exit;
}

$stmt->bind_param("i", $etkinlik_id);
if (!$stmt->execute()) {
    error_log("Fiyat sorgusu çalıştırma hatası: " . $stmt->error);
    echo json_encode(['hata' => 'Sistem hatası']);
    exit;
}

$sonuc = $stmt->get_result();
$fiyat_bilgisi = $sonuc->fetch_assoc();

if (!$fiyat_bilgisi) {
    error_log("Fiyat bilgisi bulunamadı: ID=" . $etkinlik_id);
    echo json_encode(['hata' => 'Fiyat bilgisi bulunamadı']);
    exit;
}

$fiyat = ($bilet_turu == 'tam') ? $fiyat_bilgisi['fiyat_normal'] : $fiyat_bilgisi['fiyat_ogrenci'];
$etkinlik_adi = $fiyat_bilgisi['ad'];

// Sepete ekle
$ekle_sorgu = "INSERT INTO sepet (kullanici_email, etkinlik_id, etkinlik_adi, bilet_turu, fiyat, tarih) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $baglanti->prepare($ekle_sorgu);
if (!$stmt) {
    error_log("Sepet ekleme sorgusu hazırlama hatası: " . $baglanti->error);
    echo json_encode(['hata' => 'Sistem hatası']);
    exit;
}

$stmt->bind_param("sissd", $kullanici_email, $etkinlik_id, $etkinlik_adi, $bilet_turu, $fiyat);

if (!$stmt->execute()) {
    error_log("Sepet ekleme hatası: " . $stmt->error);
    echo json_encode(['hata' => 'Sepete ekleme işlemi başarısız oldu']);
    exit;
}

// Kontenjanı güncelle
$guncelle_sorgu = "UPDATE etkinlikler SET kontenjan = kontenjan - 1 WHERE id = ?";
$stmt = $baglanti->prepare($guncelle_sorgu);
if (!$stmt) {
    error_log("Kontenjan güncelleme sorgusu hazırlama hatası: " . $baglanti->error);
    echo json_encode(['hata' => 'Sistem hatası']);
    exit;
}

$stmt->bind_param("i", $etkinlik_id);
if (!$stmt->execute()) {
    error_log("Kontenjan güncelleme hatası: " . $stmt->error);
    echo json_encode(['hata' => 'Kontenjan güncellenemedi']);
    exit;
}

echo json_encode(['mesaj' => 'Etkinlik başarıyla sepete eklendi.']);

$baglanti->close();
?>

