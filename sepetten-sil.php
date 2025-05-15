<?php
session_start();
include("baglanti.php");

// Kullanıcı emailini session'dan al
if (!isset($_SESSION['email'])) {
    echo json_encode(['hata' => 'Oturum bulunamadı. Lütfen tekrar giriş yapın.']);
    exit;
}
$kullanici_email = $_SESSION['email'];

// JSON verisini al
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['hata' => 'Geçersiz veri']);
    exit;
}

$sepet_id = $data['sepet_id'];
$etkinlik_id = $data['etkinlik_id'];

// Transaction başlat
$baglanti->begin_transaction();

try {
    // Sepetten ürünü sil (kullanıcıya özel)
    $sil_sorgu = "DELETE FROM sepet WHERE id = ? AND kullanici_email = ?";
    $stmt = $baglanti->prepare($sil_sorgu);
    $stmt->bind_param("is", $sepet_id, $kullanici_email);
    
    if (!$stmt->execute() || $stmt->affected_rows === 0) {
        throw new Exception("Sepetten silme işlemi başarısız oldu veya yetkiniz yok.");
    }

    // Kontenjanı bir artır
    $guncelle_sorgu = "UPDATE etkinlikler SET kontenjan = kontenjan + 1 WHERE id = ?";
    $stmt = $baglanti->prepare($guncelle_sorgu);
    $stmt->bind_param("i", $etkinlik_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Kontenjan güncelleme işlemi başarısız oldu.");
    }

    // Transaction'ı onayla
    $baglanti->commit();
    
    echo json_encode(['mesaj' => 'Etkinlik sepetten başarıyla silindi.']);
} catch (Exception $e) {
    // Hata durumunda transaction'ı geri al
    $baglanti->rollback();
    echo json_encode(['hata' => $e->getMessage()]);
}

$baglanti->close();
?> 