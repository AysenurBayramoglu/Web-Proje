<?php
// odeme-yap.php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: giris-yap.php");
    exit;
}
$kullanici_email = $_SESSION['email'];
include("baglanti.php");

// Frontend'den(Js ile) gönderilen ödeme ve kullanıcı biilgileri Json Formatında alınır.
$data = json_decode(file_get_contents('php://input'), true);
$kullanici_email = $data['kullanici_email'];
$odeme_yontemi = $data['odeme_yontemi'];

// Sepette ürün var mı kontrol et
$sql = "SELECT COUNT(*) as urun_sayisi FROM sepet WHERE kullanici_email = ?";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("s", $kullanici_email);
$stmt->execute();
$sonuc = $stmt->get_result();
$row = $sonuc->fetch_assoc();

if ($row['urun_sayisi'] == 0) {
    echo json_encode(['hata' => 'Sepetiniz boş!']);
    exit;
}

// Toplam tutarı hesapla
$sql = "SELECT SUM(fiyat) as toplam FROM sepet WHERE kullanici_email = ?";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("s", $kullanici_email);
$stmt->execute();
$sonuc = $stmt->get_result();
$row = $sonuc->fetch_assoc();
$toplam_tutar = $row['toplam'];

// seçilen ödeme yöntemi, girilen bilgiler ve toblam tutar 'odemeler' tablosuna kaydedilir.
$sql = "INSERT INTO odemeler (kullanici_email, odeme_yontemi, toplam_tutar";
$values = "VALUES (?, ?, ?";
$params = [$kullanici_email, $odeme_yontemi, $toplam_tutar];
$types = "ssd";

switch ($odeme_yontemi) {
    case 'krediKarti':
        $sql .= ", kart_no, son_kullanma, cvv, kart_sahibi)";
        $values .= ", ?, ?, ?, ?)";
        $params[] = $data['kart_no'];
        $params[] = $data['son_kullanma'];
        $params[] = $data['cvv'];
        $params[] = $data['kart_sahibi'];
        $types .= "ssss";
        break;
    case 'bankaHavalesi':
       $sql .= ", kart_no, son_kullanma, cvv, kart_sahibi)";
        $values .= ", ?, ?, ?, ?)";
        $params[] = $data['kart_no'];
        $params[] = $data['son_kullanma'];
        $params[] = $data['cvv'];
        $params[] = $data['kart_sahibi'];
        $types .= "ssss";
        break;
    case 'kapidaOdeme':
        $sql .= ", taksit_sayisi, kart_no, son_kullanma, cvv)";
        $values .= ", ?, ?, ?, ?)";
        $params[] = $data['taksit_sayisi'];
        $params[] = $data['kart_no'];
        $params[] = $data['son_kullanma'];
        $params[] = $data['cvv'];
        $types .= "isss";
        break;
}

$sql .= " " . $values;
$stmt = $baglanti->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    // Sepeti temizle
    $sql = "DELETE FROM sepet WHERE kullanici_email = ?";
    $stmt = $baglanti->prepare($sql);
    $stmt->bind_param("s", $kullanici_email);
    $stmt->execute();
    
    echo json_encode(['mesaj' => 'Ödeme başarıyla tamamlandı!']);
} else {
    echo json_encode(['hata' => 'Ödeme kaydedilirken bir hata oluştu: ' . $baglanti->error]);
}

$baglanti->close();
?> 
