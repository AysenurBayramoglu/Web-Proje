<?php
//bu kontrol kullanici yönetici tarafından onaylandı mı diye kontrol ediyor.
session_start();
include("baglanti.php");

// Kullanıcı giriş yaptı mı?
if (!isset($_SESSION["email"])) {
    header("Location: giris-yap.php");
    exit;
}

// Kullanıcı onaylı mı kontrolü
$email = $_SESSION["email"];
//sql sorgusu ile
$sorgu = mysqli_query($baglanti, "SELECT onayli FROM kullanicilar WHERE email='$email'");
$kullanici = mysqli_fetch_assoc($sorgu);

if ($kullanici["onayli"] != 1) {
    // Onaylı değilse oturum sonlandır.
    session_destroy();
    header("Location: giris-yap.php?onay=bekleniyor");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--bookstrap css linki-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"
        integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--bookstrap font-awesome linki-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">

    <title>ANA SAYFA2</title>
</head>

<body>
    <!-- menü kısmı başlangıç-->
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> Etkinlik Yönetim Sistemi </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!--menü kısmı sağa yaslansın (end)-->
            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#etkinlikler-container">Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#oneriler-container">Öneriler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#duyurular-container">Duyurular</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sepetim.php">Sepetim</a>
                    </li>
                </ul>

                <!-- Sağ taraftaki bilgiler -->
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="fas fa-user me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['email']); ?>
                    </span>
                    <a href="cikis-yap.php" class="btn btn-outline-light">Çıkış</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- menü kısmı bitiş-->

    <!-- Etkinlikler bölümü başlangıç -->
    <div class="container-fluid mt-5" id="etkinlikler-container">
        <h2 class="text-center mb-3 fw-bold display-5 baslik">ETKİNLİKLER</h2>
        <hr>
        <div id="etkinlikler-row" class="row row-cols-1 row-cols-md-4 g-4 mt-4">
            <!-- Etkinlikler buraya gelecek -->
        </div>
    </div>
    <!-- Etkinlikler bölümü bitiş -->

    <!-- öneriler kısmı başlangıç-->
    <div class="container-fluid position-relative mt-5" id="oneriler-container">
        <h2 class="text-center mb-3 fw-bold display-5 baslik">Önerilen Etkinlikler</h2>
        <hr>
        <button class="slider-arrow-oneri oneri-sol-ok">&#10094;</button>
        <button class="slider-arrow-oneri oneri-sag-ok">&#10095;</button>
        <div id="oneri-slider" class="d-flex overflow-auto" style="scroll-behavior: smooth; padding: 10px 60px;">

            <div class="card mx-2 flex-shrink-0" style="min-width: 300px; max-width: 300px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary">Müzik Severlere Özel</h5>
                    <p class="card-text">Müzik dinlemeyi seviyorsanız, Açık Hava Konserimiz tam size göre!</p>
                </div>
            </div>

            <div class="card mx-2 flex-shrink-0" style="min-width: 300px; max-width: 300px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-success ">Doğa Tutkunlarına</h5>
                    <p class="card-text">Doğayla iç içe kamp etkinlikleri sizi bekliyor. Bolu Gölcük Kampı tam size göre! Detaylar etkinlikler kısmında.</p>
                </div>
            </div>

            <div class="card mx-2 flex-shrink-0" style="min-width: 300px; max-width: 300px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-warning "> Gezi ve Keşif</h5>
                    <p class="card-text">Yeni yerler görmek istiyorsanız, yeşilliğe doymak isterseniz. Karadeniz Turumuzu incele!</p>
                </div>
            </div>

            <div class="card mx-2 flex-shrink-0" style="min-width: 300px; max-width: 300px;" >
                <div class="card-body">
                    <h5 class="card-title text-danger">Tatili Dört Gözle Bekleyenler</h5>
                    <p class="card-text"> "Dinlenmek ve yenilenmek isteyenler için Kapadokya Tatili güzel bir tercih olabilir.Detayları incelemeyi unutmayınız." </p>
                </div>
            </div>
        </div>
    </div>
    <!-- öneriler kısmı bitiş-->

    <!-- Duyurular bölümü -->
    <!-- ok butonlarının konumu için position ekledik.-->
    <div class="container-fluid mt-5 position-relative" id="duyurular-container" style="position: relative;">
        <h2 class="text-center mb-3 fw-bold display-5 baslik ">DUYURULAR</h2>
        <hr>
        <!-- HTML karakterleri ile Slider okları -->
        <button class="slider-arrow sol_ok">&#10094;</button>
        <button class="slider-arrow sag_ok">&#10095;</button>

        <!-- kaydırmalı duyuru kartları yana taşarsa kaydırma çubuğu ekler -->
        <div id="duyurular-slider" class="d-flex overflow-auto" style="scroll-behavior: smooth; padding: 10px 60px;">

            <?php
            //en fazla 10 duyuru tarihe göre
            $duyurular = mysqli_query($baglanti, "SELECT * FROM duyurular ORDER BY tarih DESC LIMIT 10");
            while ($duyuru = mysqli_fetch_assoc($duyurular)) {
                echo '<div class="card mx-2 flex-shrink-0 duyuru-kart" style="min-width: 300px; max-width: 300px;">';
                //kartın içeriğini saran alan.
                echo '<div class="card-body">';
                echo '<h5 class="card-title text-primary fw-bold" style="word-break: break-word;">' . htmlspecialchars($duyuru['baslik']) . '</h5>';
                echo '<p class="card-text fw-bold" style="word-break: break-word;" >' . nl2br(htmlspecialchars($duyuru['icerik'])) . '</p>';
                echo '<p class="card-text"><small class="text-muted">Tarih: ' . htmlspecialchars($duyuru['tarih']) . '</small></p>';
                echo '</div></div>';
            }
            ?>

        </div>
    </div>
    <!-- Duyurular bölümü bitiş-->

    <!--Etkinlikleri ve hava durumunun apisini çekmesi için javaScript kodu-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("etkinlik-ve-hava-api.php")
                .then(response => response.json())
                .then(data => {
                     const container = document.getElementById("etkinlikler-row"); // Değişiklik burada
                    container.innerHTML = ""; // Sadece etkinlikler row'unu temizle.

                    //her kart için yeni bir div
                    data.forEach(etkinlik => {
                        const col = document.createElement("div");
                        col.className = "col";

                        //bunlar hesaplama old. için ilk burda tuttum.
                        //hava durumu için kontrol(boş ise bilinmiyor de) 
                        const havaAciklama = etkinlik.hava_aciklama || "Bilgi yok";
                        const sicaklik = etkinlik.hava_sicaklik !== undefined ? etkinlik.hava_sicaklik + "°C" : "N/A";

                        //hava ya göre etkinlik olabilir mi?
                        const planlanamaz = ["yağmur", "Fırtına", "kar"];
                        const planlanabilirMi = !planlanamaz.some(kelime => havaAciklama.includes(kelime));

                        // Renk ve yazı ayarı
                        const planlamaRenk = planlanabilirMi ? "text-success" : "text-danger";
                        const planlamaYazi = planlanabilirMi ? "Etkinlik Planlanabilir" : "Etkinlik Planlanamaz";

                        col.innerHTML = `
                        <div class="card h-100 shadow">
                        <div class="card-body">
                            <h5 class="card-title etkinlikAd">${etkinlik.ad}</h5>
                            <hr>
                            <p class="card-text etkinlik"><strong><span class="text-danger">Etkinlik Türü:</span></strong>${etkinlik.etkinlik_turu}</p>
                            <p class="card-text etkinlik"><strong><span class="text-danger">Açıklama:</span></strong>${etkinlik.aciklama}</p>
                            <p class="card-text etkinlik"><strong><span class="text-danger">Tarih:</span></strong>${etkinlik.tarih}</p>
                             <p class="card-text etkinlik"><strong><span class="text-danger">Şehir:</span></strong>${etkinlik.sehir}</p>
                            <p class="card-text etkinlik"><strong><span class="text-danger">Kontenjan:</span></strong> ${etkinlik.kontenjan} kişi </p>
                            <p class="card-text etkinlik"><strong><span class="text-danger">Standart Fiyat:</span></strong> ${etkinlik.fiyat_normal} TL</p>
                            <p class="card-text etkinlik"><strong><span class="text-danger">Öğrenci fiyatı:</span></strong> ${etkinlik.fiyat_ogrenci} TL</p>

                            <div class="mb-2">
                                <label for="biletTuru-${etkinlik.id}"><strong>Bilet Türü:</strong></label>
                                <select class="form-select bilet-turu" id="biletTuru-${etkinlik.id}">
                                    <option value="tam">Tam - ${etkinlik.fiyat_normal} TL</option>
                                    <option value="ogrenci">Öğrenci - ${etkinlik.fiyat_ogrenci} TL</option>
                                </select>
                            </div>

                            <hr>
                             <p class="card-text etkinlik">
                                <strong><span class="text-danger">Hava Durumu:</span></strong> ${etkinlik.hava_aciklama}, ${etkinlik.hava_sicaklik}°C
                            </p>
                             <p class="card-text etkinlik ${planlamaRenk}">
                            <strong>Planlama Durumu: </strong> ${planlamaYazi}
                            </p>
                            
                            <button class="btn btn-primary w-100 mt-2" onclick="sepeteEkle(${etkinlik.id}, '${etkinlik.ad}')">
                                <i class="fas fa-shopping-cart"></i> Sepete Ekle
                            </button>
                        </div>
                    </div>`;
                        container.appendChild(col);
                    });
                })
                .catch(err => console.error("Etkinlikler alınamadı:", err));
        });
    </script>

    <!--sepete ekle butonu için -->
    <script>
        function sepeteEkle(etkinlikId, etkinlikAd) {
            const selectEl = document.getElementById(`biletTuru-${etkinlikId}`);
            const biletTuru = selectEl.value;

            fetch("sepete-ekle.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        etkinlik_id: etkinlikId,
                        bilet_turu: biletTuru
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.hata) {
                        alert(data.hata);
                    } else {
                        alert(data.mesaj);
                        // Sayfayı yenile
                        //location.reload();
                        // Kontenjanı güncelle
                        const card = document.querySelector(`button[onclick*="${etkinlikId}"]`).closest('.card');
                        const allEtkinlikElements = card.querySelectorAll('.etkinlik');
                        for (const element of allEtkinlikElements) {
                            if (element.textContent.includes('Kontenjan')) {
                                const currentKontenjan = parseInt(element.textContent.match(/\d+/)[0]);
                                element.innerHTML = element.innerHTML.replace(/\d+ kişi/, `${currentKontenjan - 1} kişi`);
                                break;
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error("Hata:", err);
                    alert("Bir hata oluştu. Lütfen tekrar deneyin.");
                });
        }
    </script>

    <!--öneriler kısmı butonu için-->
    <script>
        (function() {
            const slider = document.getElementById('oneri-slider');
            const oneri_sol_ok = document.querySelector('.oneri-sol-ok');
            const oneri_sag_ok = document.querySelector('.oneri-sag-ok');
            if (slider && oneri_sol_ok && oneri_sag_ok) {
                oneri_sol_ok.addEventListener('click', () => {
                    slider.scrollBy({
                        left: -320,
                        behavior: 'smooth'
                    });
                });

                oneri_sag_ok.addEventListener('click', () => {
                    slider.scrollBy({
                        left: 320,
                        behavior: 'smooth'
                    });
                });
            }
        })();
    </script>

    <!-- duyurular kısmındaki butonlar için js -->
    <script>
        (function() {
            const slider = document.getElementById('duyurular-slider');
            const solOk = document.querySelector('.sol_ok');
            const sagOk = document.querySelector('.sag_ok');
            //eğer bu 3 öge dom da varsa işlem yapılır.
            if (slider && solOk && sagOk) {
                solOk.addEventListener('click', () => {
                    slider.scrollBy({
                        left: -320,
                        behavior: 'smooth'
                    });
                });

                sagOk.addEventListener('click', () => {
                    slider.scrollBy({
                        left: 320,
                        behavior: 'smooth'
                    });
                });
            }
        })();
    </script>

    <!--bookstrap javaScript linki-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"
        integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>

</html>