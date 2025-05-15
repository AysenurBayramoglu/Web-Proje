<?php
session_start();//oturum verilerine erişilmesi gerekiyor.
include("baglanti.php");//baglanti bir nesnedir.

// Oturum kontrolü
if (!isset($_SESSION["email"])) {
    header("location:giris-yap.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $yeniParola = $_POST["yeni_parola"];
    $parolaTekrar = $_POST["parola_tekrar"];
    $email = $_SESSION["email"]; //email bilgisini sakla
    $mesaj = '';
    //=== hem değer hem tip kontrolu yapılır tamamen aynı olması gerek çünkü
    if ($yeniParola === $parolaTekrar) {
        $hashliParola = password_hash($yeniParola, PASSWORD_DEFAULT);
        //güvenli olması için ? konuldu. veriler doğrudan sorguya eklenmemesi için.
        $guncelle = $baglanti->prepare("UPDATE kullanicilar SET parola = ?, ilk_giris = 0 WHERE email = ?");
        //bind_param ? olan yerleri bağlamak için kullanılır.
        $guncelle->bind_param("ss", $hashliParola, $email);//ss=ikisininde string olduğunu belirtir.
        
        //sql sorgusunu çalıştırıyoruz.
        if ($guncelle->execute()) {
            $mesaj = '<div class="my-alert success-alert">Şifreniz başarıyla güncellendi. 2 saniye içinde profil sayfasına yönlendirileceksiniz.</div>';
            //mesaj gözükmesi için bir bekleme süresi verildi.
            header("refresh:2;url=kesinAnaSayfa.php");
        } else {
            $mesaj = '<div class="my-alert error-alert">Şifre güncellenirken bir hata oluştu.</div>';
        }
        $guncelle->close();
    } else {
        $mesaj = '<div class="my-alert error-alert">Girdiğiniz şifreler eşleşmiyor.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Değiştir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="ortalama">
    <div class="kayıt-blog">
        <h2>İlk Giriş Şifre Değiştirme</h2>
        <p>Güvenliğiniz için lütfen şifrenizi değiştirin.</p>
        <form action="sifre-degistirme.php" method="POST">
            <label for="yeni_parola">Yeni Şifre</label>
            <input type="password" id="yeni_parola" name="yeni_parola" required minlength="4"/>

            <label for="parola_tekrar">Şifre Tekrar</label>
            <input type="password" id="parola_tekrar" name="parola_tekrar" required minlength="4"/>

            <button type="submit">Şifreyi Değiştir</button>

            <?php if (!empty($mesaj)) echo $mesaj; ?>
        </form>
    </div>
</body>
</html>