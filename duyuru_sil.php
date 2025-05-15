<?php
session_start();
include("baglanti.php");

// Admin kontrolü
if (!isset($_SESSION["email"]) || !isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris-yap.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];
    
    $sorgu = $baglanti->prepare("DELETE FROM duyurular WHERE id = ?");
    $sorgu->bind_param("i", $id);
    //? işaretinden sonraki kısım parametrelerdir.
    if ($sorgu->execute()) {
        header("Location: yonetici-panel.php?bolum=duyuru&mesaj=duyuru_silindi");
    } else {
        header("Location: yonetici-panel.php?bolum=duyuru&hata=silme_hatasi");
    }
    $sorgu->close();
} else {
    header("Location: yonetici-panel.php?bolum=duyuru");
}
exit; 