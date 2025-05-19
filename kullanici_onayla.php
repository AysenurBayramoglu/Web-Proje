<?php
include("baglanti.php");
// onaylaya basılırsa kullanıcının id'sine göre onaylı kısmı 1 yapılır.
if (isset($_POST['onayla'])) {
    $id = intval($_POST['id']);
    mysqli_query($baglanti, "UPDATE kullanicilar SET onayli=1 WHERE id=$id");
}
elseif (isset($_POST['sil'])) {//sile basılırsa kullanıcı veri tabanından tamamen silinir.
    $id = intval($_POST['id']);
    mysqli_query($baglanti, "DELETE FROM kullanicilar WHERE id=$id");
}
header("Location: yonetici-panel.php");
exit;
?> 
