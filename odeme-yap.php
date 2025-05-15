<?php
// odeme-yap.php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: giris-yap.php");
    exit;
}
$kullanici_email = $_SESSION['email'];
include("baglanti.php");

// Sepeti boşalt
$sql = "DELETE FROM sepet WHERE kullanici_email = ?";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("s", $kullanici_email);
$stmt->execute();

// Geri bildirim
echo json_encode(['mesaj' => 'Ödeme başarıyla tamamlandı! Sepetiniz boşaltıldı.']);
?> 