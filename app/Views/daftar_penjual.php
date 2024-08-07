<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Registration Page</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url(); ?>asset/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url(); ?>asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url(); ?>asset/dists/css/adminlte.min.css">
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <a href=""><b>Daftar </b>Penjual</a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <!-- <p class="login-box-msg">Register a new membership</p> -->
                <?php $errors = session()->getFlashdata('errors') ?>
                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            <?php foreach ($errors as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <?php
                if (session()->getFlashdata('pesan')) {
                    echo ' <div class="alert alert-success" role="alert">';
                    echo session()->getFlashdata('pesan');
                    echo '</div>';
                }
                ?>

                <form action="<?= base_url(); ?>/store/penjual" method="post">
                    <?= csrf_field(); ?>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Nama Toko" name="nama_toko">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <select id="provinsi" name="provinsi" class="form-control">
                            <option>Pilih Provinsi</option>
                        </select>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-pin"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">

                        <select id="kabupaten" name="kabupaten" class="form-control">

                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-pin"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <select id="kecamatan" name="kecamatan" class="form-control">

                        </select>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-pin"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <select id="kelurahan" name="kelurahan" class="form-control">

                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-pin"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat Lengkap Toko">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-pin"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <!-- <div class="icheck-primary">
                            <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                            <label for="agreeTerms">
                                I agree to the <a href="#">terms</a>
                            </label>
                        </div> -->
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </div>
                        <!-- /.col -->
                    </div>

                </form>

                <!-- <div class="social-auth-links text-center">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-block btn-primary">
                        <i class="fab fa-facebook mr-2"></i>
                        Sign up using Facebook
                    </a>
                    <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google-plus mr-2"></i>
                        Sign up using Google+
                    </a>
                </div> -->
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.register-box -->

    <!-- jQuery -->
    <script src="<?= base_url(); ?>asset/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url(); ?>asset/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url(); ?>asset/dists/js/adminlte.min.js"></script>
    <script>
        fetch(`https://kanglerian.github.io/api-wilayah-indonesia/api/provinces.json`)
            .then(response => response.json())
            .then(provinces => {
                var data = provinces;
                var tampung = ' <option>Pilih Provinsi</option>';
                document.getElementById('kabupaten').innerHTML = '<option>Pilih</option>';
                document.getElementById('kecamatan').innerHTML = '<option>Pilih</option>';
                document.getElementById('kelurahan').innerHTML = '<option>Pilih</option>';
                data.forEach(element => {
                    tampung += `<option data-reg="${element.id}" value="${element.name}">${element.name}</option>`;
                });
                document.getElementById('provinsi').innerHTML = tampung;

            });
    </script>
    <script>
        const selectProvinsi = document.getElementById('provinsi');
        selectProvinsi.addEventListener('change', (e) => {
            var provinsi = e.target.options[e.target.selectedIndex].dataset.reg;
            fetch(`https://kanglerian.github.io/api-wilayah-indonesia/api/regencies/${provinsi}.json`)
                .then(response => response.json())
                .then(regencies => {
                    var data = regencies;
                    var tampung = ' <option>Pilih Kabupaten</option>';
                    document.getElementById('kecamatan').innerHTML = '<option>Pilih</option>';
                    document.getElementById('kelurahan').innerHTML = '<option>Pilih</option>';
                    data.forEach(element => {
                        tampung += `<option data-dist="${element.id}" value="${element.name}">${element.name}</option>`;
                    });
                    document.getElementById('kabupaten').innerHTML = tampung;

                });
        });

        const selectKabupaten = document.getElementById('kabupaten');
        selectKabupaten.addEventListener('change', (e) => {
            var kabupaten = e.target.options[e.target.selectedIndex].dataset.dist;
            fetch(`https://kanglerian.github.io/api-wilayah-indonesia/api/districts/${kabupaten}.json`)
                .then(response => response.json())
                .then(districts => {
                    var data = districts;
                    var tampung = ' <option>Pilih Kecamatan</option>';
                    document.getElementById('kelurahan').innerHTML = '<option>Pilih</option>';
                    data.forEach(element => {
                        tampung += `<option data-vill="${element.id}" value="${element.name}">${element.name}</option>`;
                    });
                    document.getElementById('kecamatan').innerHTML = tampung;

                });
        });

        const selectKecamatan = document.getElementById('kecamatan');
        selectKecamatan.addEventListener('change', (e) => {
            var kecamatan = e.target.options[e.target.selectedIndex].dataset.vill;
            fetch(`https://kanglerian.github.io/api-wilayah-indonesia/api/villages/${kecamatan}.json`)
                .then(response => response.json())
                .then(villages => {
                    var data = villages;
                    var tampung = ' <option>Pilih Kelurahan</option>';
                    data.forEach(element => {
                        tampung += `<option  value="${element.name}">${element.name}</option>`;
                    });
                    document.getElementById('kelurahan').innerHTML = tampung;

                });
        });
    </script>
</body>

</html>