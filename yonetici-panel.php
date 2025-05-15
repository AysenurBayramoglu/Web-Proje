<?php
session_start();
if (!isset($_SESSION["email"]) || !isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris-yap.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">
    <title>Yönetici Paneli</title>
    <!--bookstrap css linki-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"
        integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--bookstrap font-awesome linki-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <!-- menü kısmı başlangıç-->
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="yonetici-panel.php">Yönetici Paneli</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="yonetici-panel.php?bolum=onay">Kullanıcı Onayı</a></li>
                    <li class="nav-item"><a class="nav-link" href="yonetici-panel.php?bolum=etkinlik">Etkinlikler</a></li>
                    <li class="nav-item"><a class="nav-link" href="yonetici-panel.php?bolum=duyuru">Duyurular</a></li>
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

    <div class="container mt-5 pt-5">
        <?php
        include("baglanti.php");
        //bu bölüm kısmı hem onay hem etkinlik hem de duyuru kısmı if lerle oluşturuldu. varsayılanı onaydır.
        $bolum = $_GET['bolum'] ?? 'onay';
        if ($bolum == 'onay') {
            // Kullanıcı Onayla Bölümü
            echo '<div class="card mb-4"><div class="card-header bg-primary text-white">Kullanıcı Onayla</div><div class="card-body">';
            $bekleyenler = mysqli_query($baglanti, "SELECT * FROM kullanicilar WHERE onayli=0 AND rol='kullanici'");
            if (mysqli_num_rows($bekleyenler) == 0) {
                echo "<p>Onay bekleyen kullanıcı yok.</p>";
            } else {
                echo '<table class="table table-bordered">
                <tr>
                    <th>Email</th>
                    <th>İşlem</th>
                </tr>';
                while ($kullanici = mysqli_fetch_assoc($bekleyenler)) {
                    //form da kullanıcı_onayla.php dosyasına iletilir.
                    //2 farklı form var biri onaylamak için biri silmek için.
                    echo "<tr>
                        <td>{$kullanici['email']}</td>
                        <td>
                            <form action='kullanici_onayla.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='{$kullanici['id']}'>

                                <button type='submit' name='onayla' class='btn btn-success btn-sm'>Onayla</button>
                            </form>

                            <form action='kullanici_onayla.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='{$kullanici['id']}'>
                                <button type='submit' name='sil' class='btn btn-danger btn-sm'>Sil</button>
                            </form>
                        </td>
                    </tr>";
                }
                echo '</table>';
            }
            echo '</div></div>'; //üstteki div leri kapattık.Php içeriği olduğu için tırnak içinde.

        } elseif ($bolum == 'etkinlik') {
            // Etkinlik Ekle/düzenle/Listele Bölümü
            echo '<div class="card mb-4"><div class="card-header bg-success text-white">Etkinlik Ekle</div><div class="card-body">';

            // Etkinlik düzenleme işlemi
            if (isset($_GET['duzenle'])) {
                $id = $_GET['duzenle'];
                //id si gönderilen etkinlik alınır.
                $etkinlik = mysqli_query($baglanti, "SELECT * FROM etkinlikler WHERE id = $id");
                if (mysqli_num_rows($etkinlik) > 0) {
                    $etkinlik = mysqli_fetch_assoc($etkinlik);

                    // Form gönderilmişse etkinlik düzenle alanı varsa çalışır.
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["etkinlik_duzenle"])) {
                        //veriler alınır.
                        // Güncelleme işlemi yapılır
                        $ad = $_POST["ad"];
                        $etkinlik_turu = $_POST["etkinlik_turu"];
                        $aciklama = $_POST["aciklama"];
                        $tarih = $_POST["tarih"];
                        $kontenjan = intval($_POST["kontenjan"]);
                        $fiyat_normal = floatval($_POST["fiyat_normal"]);
                        $fiyat_ogrenci = floatval($_POST["fiyat_ogrenci"]);
                        $sehir = $_POST["sehir"];

                        //var olan etkinliği düzenler. UPDATE(bunun sayesinde veri tabanında değişiklik yapılır.)
                        $sorgu = $baglanti->prepare("UPDATE etkinlikler SET ad=?, etkinlik_turu=?, aciklama=?, tarih=?, kontenjan=?, fiyat_normal=?, fiyat_ogrenci=?, sehir=? WHERE id=?");
                        $sorgu->bind_param("ssssiddsi", $ad, $etkinlik_turu, $aciklama, $tarih, $kontenjan, $fiyat_normal, $fiyat_ogrenci, $sehir, $id); //sql de ? yerlerine birim atamak için.
                        //sonuç gösterilir.
                        if ($sorgu->execute()) {
                            echo '<div class="alert alert-success">Etkinlik başarıyla güncellendi.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Etkinlik güncellenirken bir hata oluştu.</div>';
                        }
                        $sorgu->close();
                    }

                    // adı=etkinlik_duzenle. Düzenleme formu
                    //önceden var olan veriler alınır ve gösterilir.
                    echo '<form method="POST" class="row g-3">
                        <input type="hidden" name="etkinlik_duzenle" value="1">
                        <div class="col-md-6">
                            <label for="ad" class="form-label">Etkinlik Adı</label>
                            <input type="text" class="form-control" id="ad" name="ad" value="' . htmlspecialchars($etkinlik['ad']) . '" required>
                        </div>
                        <div class="col-md-6">
                            <label for="etkinlik_turu" class="form-label">Etkinlik Türü</label>
                            <input type="text" class="form-control" id="etkinlik_turu" name="etkinlik_turu" value="' . htmlspecialchars($etkinlik['etkinlik_turu']) . '" required>
                        </div>
                        <div class="col-12">
                            <label for="aciklama" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="aciklama" name="aciklama" required>' . htmlspecialchars($etkinlik['aciklama']) . '</textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="tarih" class="form-label">Tarih</label>
                            <input type="date" class="form-control" id="tarih" name="tarih" value="' . $etkinlik['tarih'] . '" required>
                        </div>
                        <div class="col-md-2">
                            <label for="kontenjan" class="form-label">Kontenjan</label>
                            <input type="number" class="form-control" id="kontenjan" name="kontenjan" value="' . $etkinlik['kontenjan'] . '" required>
                        </div>
                        <div class="col-md-3">
                            <label for="fiyat_normal" class="form-label">Fiyat (Normal)</label>
                            <input type="number" step="0.01" class="form-control" id="fiyat_normal" name="fiyat_normal" value="' . $etkinlik['fiyat_normal'] . '" required>
                        </div>
                        <div class="col-md-3">
                            <label for="fiyat_ogrenci" class="form-label">Fiyat (Öğrenci)</label>
                            <input type="number" step="0.01" class="form-control" id="fiyat_ogrenci" name="fiyat_ogrenci" value="' . $etkinlik['fiyat_ogrenci'] . '" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sehir" class="form-label">Şehir</label>
                            <input type="text" class="form-control" id="sehir" name="sehir" value="' . htmlspecialchars($etkinlik['sehir']) . '" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                            </button>
                            <a href="yonetici-panel.php?bolum=etkinlik" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                        </div>
                    </form>';
                } else {
                    echo '<div class="alert alert-danger">Etkinlik bulunamadı.</div>';
                }
            } else {
                // Normal etkinlik ekleme formu(eğer düzenleme yapılmıyorsa ekleme yapılıyor demektir.)
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["etkinlik_ekle"])) {
                    $ad = $_POST["ad"];
                    $etkinlik_turu = $_POST["etkinlik_turu"];
                    $aciklama = $_POST["aciklama"];
                    $tarih = $_POST["tarih"];
                    $kontenjan = intval($_POST["kontenjan"]);
                    $fiyat_normal = floatval($_POST["fiyat_normal"]);
                    $fiyat_ogrenci = floatval($_POST["fiyat_ogrenci"]);
                    $sehir = $_POST["sehir"];
                    //INSET sorgusuyla veri tabanına ekler.
                    $sorgu = $baglanti->prepare("INSERT INTO etkinlikler (ad, etkinlik_turu, aciklama, tarih, kontenjan, fiyat_normal, fiyat_ogrenci, sehir) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $sorgu->bind_param("ssssidds", $ad, $etkinlik_turu, $aciklama, $tarih, $kontenjan, $fiyat_normal, $fiyat_ogrenci, $sehir);
                    if ($sorgu->execute()) {
                        echo '<div class="alert alert-success">Etkinlik başarıyla eklendi.</div>';
                    } else {
                        echo '<div class="alert alert-danger">Etkinlik eklenirken hata oluştu.</div>';
                    }
                    $sorgu->close();
                }
        ?>
                <!-- burda echo ile yazmadım ama düzenle ile aynı mantıktadır. -->
                <form method="POST" class="row g-3">
                    <input type="hidden" name="etkinlik_ekle" value="1">
                    <div class="col-md-6">
                        <label for="ad" class="form-label">Etkinlik Adı</label>
                        <input type="text" class="form-control" id="ad" name="ad" required>
                    </div>
                    <div class="col-md-6">
                        <label for="etkinlik_turu" class="form-label">Etkinlik Türü</label>
                        <input type="text" class="form-control" id="etkinlik_turu" name="etkinlik_turu" required>
                    </div>
                    <div class="col-12">
                        <label for="aciklama" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="aciklama" name="aciklama" required></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="tarih" class="form-label">Tarih</label>
                        <input type="date" class="form-control" id="tarih" name="tarih" required>
                    </div>
                    <div class="col-md-2">
                        <label for="kontenjan" class="form-label">Kontenjan</label>
                        <input type="number" class="form-control" id="kontenjan" name="kontenjan" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fiyat_normal" class="form-label">Fiyat (Normal)</label>
                        <input type="number" step="0.01" class="form-control" id="fiyat_normal" name="fiyat_normal" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fiyat_ogrenci" class="form-label">Fiyat (Öğrenci)</label>
                        <input type="number" step="0.01" class="form-control" id="fiyat_ogrenci" name="fiyat_ogrenci" required>
                    </div>
                    <div class="col-md-6">
                        <label for="sehir" class="form-label">Şehir</label>
                        <input type="text" class="form-control" id="sehir" name="sehir" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Etkinlik Ekle</button>
                    </div>
                </form>
                <hr>
            <!--mevcut etkinlikler için tablo oluştur.-->
                <h5>Mevcut Etkinlikler</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Ad</th>
                        <th>Tür</th>
                        <th>Tarih</th>
                        <th>Şehir</th>
                        <th>Kontenjan</th>
                        <th>Fiyat (Normal)</th>
                        <th>Fiyat (Öğrenci)</th>
                        <th>İşlem</th>
                    </tr>

                    <?php
                    include("baglanti.php");
                    //tarih sırasına göre
                    $etkinlikler = mysqli_query($baglanti, "SELECT * FROM etkinlikler ORDER BY tarih DESC");
                    while ($etkinlik = mysqli_fetch_assoc($etkinlikler)) {
                        echo "<tr>
                            <td>{$etkinlik['ad']}</td>
                            <td>{$etkinlik['etkinlik_turu']}</td>
                            <td>{$etkinlik['tarih']}</td>
                            <td>{$etkinlik['sehir']}</td>
                            <td>{$etkinlik['kontenjan']}</td>
                            <td>{$etkinlik['fiyat_normal']}</td>
                            <td>{$etkinlik['fiyat_ogrenci']}</td>
                            <td>
                                <a href='yonetici-panel.php?bolum=etkinlik&duzenle={$etkinlik['id']}' class='btn btn-warning btn-sm me-2'>
                                    <i class='fas fa-edit'></i> Düzenle
                                </a>
                                
                                <form action='etkinlik_sil.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='{$etkinlik['id']}'>
                                    <button type='submit' class='btn btn-danger btn-sm' onclick=\"return confirm('Bu etkinliği silmek istediğinize emin misiniz?')\">
                                        <i class='fas fa-trash'></i> Sil
                                    </button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>

                </table>
            <?php
            }
            echo '</div></div>';
        } elseif ($bolum == 'duyuru') {
            // Duyuru Ekle/Listele Bölümü
            echo '<div class="card mb-4"><div class="card-header bg-warning text-dark">Duyuru Ekle</div><div class="card-body">';
            // Duyuru ekleme işlemi
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["duyuru_ekle"])) {
                $baslik = $_POST["baslik"];
                $icerik = $_POST["icerik"];
                $olusturan_id = $_SESSION["email"];
                $stmt = $baglanti->prepare("INSERT INTO duyurular (baslik, icerik) VALUES (?, ?)");
                $stmt->bind_param("ss", $baslik, $icerik);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-success">Duyuru başarıyla eklendi.</div>';
                } else {
                    echo '<div class="alert alert-danger">Duyuru eklenirken hata oluştu.</div>';
                }
                $stmt->close();
            }
            ?>
            <form method="POST" class="row g-3">
                <input type="hidden" name="duyuru_ekle" value="1">
                <div class="col-md-12">
                    <label for="baslik" class="form-label">Başlık</label>
                    <input type="text" class="form-control" id="baslik" name="baslik" required>
                </div>
                <div class="col-md-6">
                    <label for="tarih" class="form-label">Tarih</label>
                    <input type="date" class="form-control" id="tarih" name="tarih" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-12">
                    <label for="icerik" class="form-label">İçerik</label>
                    <textarea class="form-control" id="icerik" name="icerik" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-warning">Duyuru Ekle</button>
                </div>
            </form>
            <hr>
            <h5>Mevcut Duyurular</h5>
            <table class="table table-bordered">
                <tr>
                    <th>Başlık</th>
                    <th>İçerik</th>
                    <th>Tarih</th>

                    <th>İşlem</th>
                </tr>
                <?php
                $duyurular = mysqli_query($baglanti, "SELECT * FROM duyurular ORDER BY tarih DESC");
                while ($duyuru = mysqli_fetch_assoc($duyurular)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($duyuru['baslik']) . "</td>
                        <td>" . htmlspecialchars($duyuru['icerik']) . "</td>
                        <td>" . htmlspecialchars($duyuru['tarih']) . "</td>
                        
                        <td>
                        <form action='duyuru_sil.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='" . $duyuru['id'] . "'>
                            <button type='submit' class='btn btn-danger btn-sm' onclick=\"return confirm('Bu duyuruyu silmek istediğinize emin misiniz?')\">Sil</button>
                        </form>
                    </td>
                    </tr>";
                }
                ?>
            </table>
        <?php
            echo '</div></div>';
        }
        ?>
    </div>

    <!--bookstrap javaScript linki-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"
        integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>