<?php
//veri tabanı bağlantısı ekle.
include("baglanti.php");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  //email ve parolayı al
  $email = $_POST["email"];
  $password= password_hash($_POST["parola"], PASSWORD_DEFAULT);
  $mesaj = '';
  //bilgileri kullanıcı tablosuna ekle
  $ekle="INSERT INTO kullanicilar (email, parola) VALUES ('$email','$password')";

  //veri tabanına kayıt ekle (1.parametre=veri tabanı bağlantısı 2.parametre=çalıştırılacak sql sorgusu)
  $calistirekle= mysqli_query($baglanti,$ekle);
  mysqli_close($baglanti);

  if ($calistirekle) {
    $mesaj = '<div class="my-alert success-alert">Kayıt başarılı bir şekilde gerçekleşti.</div>';
    header("location:giris-yap.php");
} else {
    $mesaj = '<div class="my-alert error-alert">Kayıt eklenirken bir problem oluştu!</div>';
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KAYIT OLMA İŞLEMİ</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="ortalama">
  <div class="kayıt-blog">
    <h2>Kayıt Ol</h2>
    <!-- burdaki action php eklendiğinde php dosyasının uzantısı olucak-->
    <form action="kayıt-ol.php" method="POST">
        
      <label for="email">E-posta</label>
      <input type="email" id="email" name="email" required />

      <label for="parola">Şifre</label>
      <input type="password" id="parola" name="parola" required minlength="4"/>

      <button type="submit" name="kayit_ol">Kayıt Ol</button>

      <?php if (!empty($mesaj)) echo $mesaj; ?>
    </form>
  </div>
</body>
</html>

