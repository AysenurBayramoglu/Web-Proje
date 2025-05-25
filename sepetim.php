<?php
// sepetim.php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: giris-yap.php");
    exit;
}
$kullanici_email = $_SESSION['email'];
include("baglanti.php");

//  Sepet verilerini çek
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
            <a class="navbar-brand" href="#"> Etkinlik Yönetim Sistemi </a>
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

    <!-- Sepet içeriği başlangıç (bu değişmez) -->
    <div class="container mt-5 pt-5">
        <h2 class="text-center mb-4">Sepetim</h2>
        <div class="row"> <!--grid yapısı-->
            <div class="col-md-12">
                <!-- Sepet ürünleri buraya gelecek -->
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Sepet içeriği gösterimi -->
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

    <!--burası sepet özeti ve ödeme yöntemlerinin olduğu kısım-->
    <div class="container mt-5 pt-5">
        <div class="row"> <!--grid yapısı-->
            <div class="col-md-12"><!--sayfanın tamamını kaplasın-->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Sepet Özeti</h5>
                        <p class="card-text">Toplam: <?= isset($toplam) ? $toplam : 0 ?> TL</p>

                        <!-- Ödeme Yöntemleri -->
                        <h6 class="mt-3">Ödeme Yöntemi Seçin:</h6>
                        <form id="odemeFormu">
                            <div class="form-check">
                                <!-- radio kısımları yanı seçim kısımları burda-->
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

                            <!-- Kredi Kartı Formu -->
                            <div id="krediKartiForm" class="payment-form mt-3">
                                <div class="mb-3">
                                    <label for="kartNo" class="form-label">Kart Numarası</label>
                                    <input type="text" class="form-control" id="kartNo" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sonKullanma" class="form-label">Son Kullanma Tarihi</label>
                                        <input type="text" class="form-control" id="sonKullanma" placeholder="AA/YY" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="kartSahibi" class="form-label">Kart Sahibinin Adı</label>
                                    <input type="text" class="form-control" id="kartSahibi" placeholder="Ad Soyad">
                                </div>
                            </div>

                            <!-- Banka Kartı Formu -->
                            <div id="bankaHavalesiForm" class="payment-form mt-3" style="display: none;">
                                <div class="mb-3">
                                    <label for="kartNoBanka" class="form-label">Kart Numarası</label>
                                    <input type="text" class="form-control" id="kartNoBanka" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sonKullanmaBanka" class="form-label">Son Kullanma Tarihi</label>
                                        <input type="text" class="form-control" id="sonKullanmaBanka" placeholder="AA/YY" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvvBanka" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvvBanka" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="kartSahibiBanka" class="form-label">Kart Sahibinin Adı</label>
                                    <input type="text" class="form-control" id="kartSahibiBanka" placeholder="Ad Soyad">
                                </div>
                            </div>

                            <!-- Taksit Formu -->
                            <div id="kapidaOdemeForm" class="payment-form mt-3" style="display: none;">
                                <div class="mb-3">
                                    <label for="taksitSayisi" class="form-label">Taksit Sayısı</label>
                                    <select class="form-select" id="taksitSayisi">
                                        <option value="2">2 Taksit</option>
                                        <option value="3">3 Taksit</option>
                                        <option value="6">6 Taksit</option>
                                        <option value="9">9 Taksit</option>
                                        <option value="12">12 Taksit</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="kartNoTaksit" class="form-label">Kart Numarası</label>
                                    <input type="text" class="form-control" id="kartNoTaksit" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sonKullanmaTaksit" class="form-label">Son Kullanma Tarihi</label>
                                        <input type="text" class="form-control" id="sonKullanmaTaksit" placeholder="AA/YY" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvvTaksit" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvvTaksit" placeholder="123" maxlength="3">
                                    </div>
                                </div>
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
            // Sepet boş mu kontrol et
            if (document.querySelectorAll('.card-body .mb-3').length === 0) {
                alert('Sepetiniz boş!');
                return;
            }

            // Seçili ödeme yöntemini al
            const odemeYontemi = document.querySelector('input[name="odeme_yontemi"]:checked').value;
            let formData = {
                kullanici_email: '<?= $kullanici_email ?>',
                odeme_yontemi: odemeYontemi
            };

            // Form alanlarını kontrol et
            let isValid = true;//geçerli mi?
            switch (odemeYontemi) {
                case 'krediKarti':
                    const kartNo = document.getElementById('kartNo').value;
                    const sonKullanma = document.getElementById('sonKullanma').value;
                    const cvv = document.getElementById('cvv').value;
                    const kartSahibi = document.getElementById('kartSahibi').value;

                    if (!kartNo || !sonKullanma || !cvv || !kartSahibi) {
                        alert('Lütfen tüm kart bilgilerini doldurun!');
                        isValid = false;
                    } else {
                        formData.kart_no = kartNo;
                        formData.son_kullanma = sonKullanma;
                        formData.cvv = cvv;
                        formData.kart_sahibi = kartSahibi;
                    }
                    break;

                case 'bankaHavalesi':
                    const kartNoBanka = document.getElementById('kartNoBanka').value;
                    const sonKullanmaBanka = document.getElementById('sonKullanmaBanka').value;
                    const cvvBanka = document.getElementById('cvvBanka').value;
                    const kartSahibiBanka = document.getElementById('kartSahibiBanka').value;

                    if (!kartNoBanka || !sonKullanmaBanka || !cvvBanka || !kartSahibiBanka) {
                        alert('Lütfen tüm kart bilgilerini doldurun!');
                        isValid = false;
                    } else {
                        formData.kart_no = kartNoBanka;
                        formData.son_kullanma = sonKullanmaBanka;
                        formData.cvv = cvvBanka;
                        formData.kart_sahibi = kartSahibiBanka;
                    }
                    break;

                case 'kapidaOdeme':
                    const taksitSayisi = document.getElementById('taksitSayisi').value;
                    const kartNoTaksit = document.getElementById('kartNoTaksit').value;
                    const sonKullanmaTaksit = document.getElementById('sonKullanmaTaksit').value;
                    const cvvTaksit = document.getElementById('cvvTaksit').value;

                    if (!kartNoTaksit || !sonKullanmaTaksit || !cvvTaksit) {
                        alert('Lütfen tüm kart bilgilerini doldurun!');
                        isValid = false;
                    } else {
                        formData.taksit_sayisi = taksitSayisi;
                        formData.kart_no = kartNoTaksit;
                        formData.son_kullanma = sonKullanmaTaksit;
                        formData.cvv = cvvTaksit;
                    }
                    break;
            }

            if (!isValid) return;

            if (confirm('Ödeme yapmak istediğinizden emin misiniz?')) {
                fetch('odeme-yap.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                //ödeme yap dosyasından sonra gelen yanıtı işler
                // Sunucudan gelen yanıtı JSON formatına çevirir
                .then(response => response.json())
                .then(data => {
                    if (data.hata) {
                        alert(data.hata);
                    } else {
                        alert('Ödeme başarıyla tamamlandı!');
                        location.reload();
                    }
                })
                .catch(err => {
                    console.error('Hata:', err);
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                });
            }
        }

        // Ödeme formlarını göster/gizle
        document.querySelectorAll('input[name="odeme_yontemi"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Tüm formları gizle
                document.querySelectorAll('.payment-form').forEach(form => {
                    form.style.display = 'none';
                });

                // Seçilen formu göster
                const selectedForm = document.getElementById(this.value + 'Form');
                if (selectedForm) {
                    selectedForm.style.display = 'block';
                }
            });
        });

        //kart blgileri daha kolay girilsin diye formatını hazırla.
        // Kart numarası formatı
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            input.value = formattedValue;
        }

        // Son kullanma tarihi formatı
        function formatExpiryDate(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value;
        }

        // Kart bilgilerinde özel formatlar
        document.getElementById('kartNo').addEventListener('input', function() {
            formatCardNumber(this);
        });
        document.getElementById('kartNoTaksit').addEventListener('input', function() {
            formatCardNumber(this);
        });
        document.getElementById('kartNoBanka').addEventListener('input', function() {
            formatCardNumber(this);
        });
        document.getElementById('sonKullanma').addEventListener('input', function() {
            formatExpiryDate(this);
        });
        document.getElementById('sonKullanmaTaksit').addEventListener('input', function() {
            formatExpiryDate(this);
        });
        document.getElementById('sonKullanmaBanka').addEventListener('input', function() {
            formatExpiryDate(this);
        });
    </script>

    <!--bookstrap javaScript linki-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"
        integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>
<?php $baglanti->close(); ?>
