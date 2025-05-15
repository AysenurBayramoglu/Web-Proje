<?php
include("baglanti.php");

if (isset($_POST['onayla'])) {
    //kullanıcı id sini alır.
    $id = intval($_POST['id']);
    //o id ye ait kullanıcıyı onaylar.
    mysqli_query($baglanti, "UPDATE kullanicilar SET onayli=1 WHERE id=$id");
} elseif (isset($_POST['sil'])) {
    $id = intval($_POST['id']);
    mysqli_query($baglanti, "DELETE FROM kullanicilar WHERE id=$id");
}
header("Location: yonetici-panel.php");
exit;
?> 