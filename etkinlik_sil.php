<?php
session_start();
include("baglanti.php");

// Admin kontrolü
if (!isset($_SESSION["email"]) || !isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris-yap.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Prepared statement kullanımı
    $stmt = $baglanti->prepare("DELETE FROM etkinlikler WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: yonetici-panel.php?bolum=etkinlik");
exit;
?> 
