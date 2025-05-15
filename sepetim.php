<?php
// sepetim.php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: giris-yap.php");
    exit;
}
$kullanici_email = $_SESSION['email'];
include("baglanti.php");

// 2. Sepet verilerini çek
$sql = "SELECT s.*, e.ad AS etkinlik_adi FROM sepet s 
        JOIN etkinlikler e ON s.etkinlik_id = e.id
        WHERE s.kullanici_email = ?
        ORDER BY s.tarih DESC";
$stmt = $baglanti->prepare($sql);
$stmt->bind_param("s", $kullanici_email);
$stmt->execute();
$sonuc = $stmt->get_result();
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

    <title>Sepetim</title>
</head>

<body>
    <!-- menü kısmı başlangıç-->
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> PROJE </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!--menü kısmı sağa yaslansın (end)-->
            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav">
                   <li class="nav-item">
                        <a class="nav-link" href="kesinAnaSayfa.php">Ana Sayfa</a>
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
                        <i class="fas fa-user me-1"></i><!-- font awesome ikonu-->
                        <?php echo htmlspecialchars($_SESSION['email']); ?>
                    </span>
                    <a href="cikis-yap.php" class="btn btn-outline-light">Çıkış</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- menü kısmı bitiş-->

    <!-- Sepet içeriği başlangıç -->
    <div class="container mt-5 pt-5">
        <h2 class="text-center mb-4">Sepetim</h2>
        <div class="row"> <!--grid yapısı-->
            <div class="col-md-12">
                <!-- Sepet ürünleri buraya gelecek -->
                <div class="card mb-3">
                    <div class="card-body">
                        <?php if ($sonuc->num_rows > 0): ?>
                            <?php $toplam = 0; ?>
                            <?php while ($satir = $sonuc->fetch_assoc()): ?>
                                <div class="mb-3 border-bottom pb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5><?= htmlspecialchars($satir['etkinlik_adi']) ?></h5>
                                            <p>Bilet Türü: <?= $satir['bilet_turu'] ?> | Fiyat: <?= $satir['fiyat'] ?> TL</p>
                                            <p class="text-muted small">Eklenme Tarihi: <?= $satir['tarih'] ?></p>
                                        </div>
                                        <button class="btn btn-danger btn-sm" onclick="sepettenSil(<?= $satir['id'] ?>, <?= $satir['etkinlik_id'] ?>)">
                                            <i class="fas fa-trash"></i> Sil
                                        </button>
                                    </div>
                                </div>
                                <?php $toplam += $satir['fiyat']; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <h5 class="card-title">Sepetiniz şu anda boş</h5>
                            <p class="card-text">Etkinliklerden seçim yaparak sepetinizi doldurabilirsiniz.</p>
                            <a href="kesinAnaSayfa.php" class="btn btn-primary">Etkinliklere Git</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sepet içeriği bitiş -->

    <div class="container mt-5 pt-5">
        <div class="row"> <!--grid yapısı-->
            <div class="col-md-12"><!--sayfanın tamamını kaplasın-->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Sepet Özeti</h5>
                        <p class="card-text">Toplam: <?= isset($toplam) ? $toplam : 0 ?> TL</p>

                        <!-- Ödeme Yöntemleri -->
                        <h6 class="mt-3">Ödeme Yöntemi Seçin:</h6>
                        <form>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="odeme_yontemi" id="krediKarti" value="krediKarti" checked>
                                <label class="form-check-label" for="krediKarti">Kredi Kartı</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="odeme_yontemi" id="bankaHavalesi" value="bankaHavalesi">
                                <label class="form-check-label" for="bankaHavalesi">Banka Kartı</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="odeme_yontemi" id="kapidaOdeme" value="kapidaOdeme">
                                <label class="form-check-label" for="kapidaOdeme">Taksit</label>
                            </div>
                        </form>

                        <button class="btn btn-success w-100 mt-3" onclick="odemeYap()">Ödeme Yap</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    


    <!-- Sepetten silme işlemi için JavaScript -->
    <script>
        function sepettenSil(sepetId, etkinlikId) {
            if (confirm('Bu etkinliği sepetten silmek istediğinizden emin misiniz?')) {
                fetch('sepetten-sil.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            sepet_id: sepetId,
                            etkinlik_id: etkinlikId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.hata) {
                            alert(data.hata);
                        } else {
                            alert(data.mesaj);
                            // Sayfayı yenile
                            location.reload();
                        }
                    })
                    .catch(err => {
                        console.error('Hata:', err);
                        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                    });
            }
        }

        function odemeYap() {
            if (confirm('Ödeme yapmak istediğinizden emin misiniz?')) {
                fetch('odeme-yap.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        kullanici_email: '<?= $kullanici_email ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.hata) {
                        alert(data.hata);
                    } else {
                        alert('Ödeme başarıyla tamamlandı! Sepetiniz boşaltıldı.');
                        location.reload();
                    }
                })
                .catch(err => {
                    console.error('Hata:', err);
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                });
            }
        }
    </script>

    <!--bookstrap javaScript linki-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"
        integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>
<?php $baglanti->close(); ?>