<?php
include("baglanti.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $email = $_POST["email"];
  $parola = ($_POST["parola"]);

  //2 si de doluysa-tanımlanmışsa ilk email ile kullanıcı aranır.
  if (isset($email) && isset($parola)) {


    $secim = "SELECT * FROM kullanicilar WHERE email = '$email'";
    $calistir = mysqli_query($baglanti, $secim); //veri sorgulama
    $kayitsayisi = mysqli_num_rows($calistir); // ya 0 ya 1 olabilir.

    if ($kayitsayisi > 0) {
      //o satırı dizi olarak getirir
      $ilgiliKayit = mysqli_fetch_assoc($calistir);
      $hashliSifre = $ilgiliKayit["parola"];
      $ilkGiris = $ilgiliKayit["ilk_giris"];

      //verify:doğrulamak eşleşiyorlar mı bak.
      if (password_verify($parola, $hashliSifre)) {
        //oturumu başlat
        session_start();
        //kullanıcının oturum bilgisini saklar.
        $_SESSION["email"] = $ilgiliKayit["email"];
        $_SESSION["rol"] = $ilgiliKayit["rol"]; // Rolü de oturuma kaydet.
        if ($ilkGiris == 1) {
          header("location:sifre-degistirme.php");
          exit;
        } else {
          if ($ilgiliKayit["rol"] == "admin") {
            header("location:yonetici-panel.php");
            exit;
          } else {
            header("location:kesinAnaSayfa.php");
            exit;
          }
        }
      } else {
        $mesaj = '<div class="my-alert error-alert"> Girdiğiniz parola yanlış.</div>';
      }
    } else {
      $mesaj = '<div class="my-alert error-alert"> Girdiğiniz email adresi yanlış.</div>';
    }
  }
  mysqli_close($baglanti);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GİRİŞ YAPINIZ</title>
  <link rel="stylesheet" href="style.css">
</head>

<body class="ortalama">
  <div class="kayıt-blog">
    <h2>Giriş Yap</h2>
    <!-- burdaki action php eklendiğinde php dosyasının uzantısı olucak-->
    <form action="giris-yap.php" method="POST">

      <label for="email">E-posta</label>
      <input type="email" id="email" name="email" required />

      <label for="password">Şifre</label>
      <input type="password" id="parola" name="parola" required />

      <!-- eğer yönetici onaylamadıysa giriş yap ekranında uyarı verilir. -->
      <button type="submit">Giriş Yap</button>
      <?php
      if (isset($_GET['onay']) && $_GET['onay'] == 'bekleniyor') {
        echo '<div class="my-alert error-alert">Hesabınız henüz yönetici tarafından onaylanmadı.</div>';
      }
      ?>

      <?php if (!empty($mesaj)) echo $mesaj; ?>
      <p>Hesabınız yok mu? <a href="kayıt-ol.php">Kayıt Ol</a></p>
    </form>
  </div>
</body>

</html>