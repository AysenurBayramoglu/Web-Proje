<?php
session_start();
//içerideki verileri boşaltıyoruz boş veri atıyoruz da denebilir.
$_SESSION=array();
session_destroy();//oturumu sonlandır.
//gideceği yeri söylüyoruz.
header("location:giris-yap.php");
exit; // Güvenlik için exit ekliyoruz
?> 
