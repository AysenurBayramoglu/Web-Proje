<?php
include("baglanti.php");
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    mysqli_query($baglanti, "DELETE FROM etkinlikler WHERE id=$id");
}
header("Location: yonetici-panel.php?bolum=etkinlik");
exit;
?> 