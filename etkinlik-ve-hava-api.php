<?php
header("Content-Type: application/json");//tarayıcıya bu dosyanın JSON formatında veri döndürdüğünü söyler.
include("baglanti.php");

$apiKey = "5b8ff43e3d2b74411bedda73b2fcd960";

// tarih sırasına göre sıralamak için
$sorgu = mysqli_query($baglanti, "SELECT * FROM etkinlikler ORDER BY tarih ASC");
$etkinlikler = [];

while ($etkinlik = mysqli_fetch_assoc($sorgu)) {
    $sehir = $etkinlik['sehir'];

    // OpenWeather API'den hava durumu bilgisi çek
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($sehir) . "&appid=$apiKey&units=metric&lang=tr";

    $havaVeri = file_get_contents($url); // API'den veri al.
    $hava = json_decode($havaVeri, true); // JSON'u php veri yapısına çevirir.

    // etkinlik bilgisine hava durumunda sıcaklık ve hava bilgisi varsa eklenir. 
    if (isset($hava['main']['temp']) && isset($hava['weather'][0]['description'])) {
        $etkinlik['hava_sicaklik'] = round($hava['main']['temp']);
        $etkinlik['hava_aciklama'] = $hava['weather'][0]['description'];
    } else {
        $etkinlik['hava_sicaklik'] = '?';
        $etkinlik['hava_aciklama'] = 'Bilinmiyor';
    }
    
    $etkinlikler[] = $etkinlik;
}

//en son php yi json a çevirdik javaScripte göndermek için
echo json_encode($etkinlikler, JSON_UNESCAPED_UNICODE);
?>

