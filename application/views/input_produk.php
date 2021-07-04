<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Produk</title>

    <link rel="stylesheet" href="<?= base_url("vendor/bootstrap/4.4.1/css/bootstrap.min.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("vendor/sweetalert2/sweetalert2.min.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("vendor/fontawesome-free-5.9.0-web/css/all.min.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("vendor/select2-4.0.6-rc.1/dist/css/select2.min.css"); ?>">

    <script src="<?= base_url("assets/js/jquery-3.4.1.min.js"); ?>"></script>
</head>

<body style="background-color: antiquewhite;">
    <style>
    .loader {
        background-color: rgba(248, 247, 216, 0.7);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }

    .loader-image {
        display: block;
        margin: 0 auto 0 auto;
    }
    </style>
    <div class="loader">
        <div class="loader-image" style="margin-top: 20vh;">
            <img src="<?= base_url("assets\loader\atomic_loader.svg"); ?>" \ class="loader-image">
            <p class="h3 text-center mb-3">Silahkan tunggu...</p>
        </div>
    </div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Input Data Produk</h1>
                <?= form_open_multipart("Admin/ex_input_produk", 'id="form_input_produk"'); ?>

                <div class="form-group">
                    <label for="id_toko">Kode Toko</label>
                    <select class="form-control" id="id_toko" name="id_toko" required>
                        <option value=""></option>
                        <?php foreach ($toko as $t) : ?>
                        <option value="<?= $t["id"]; ?>"><?= $t["nama"]; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="error-text error-id_toko text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="kode">Kode Produk</label>
                    <input type="number" class="form-control" id="kode" name="kode" required>
                    <small class="error-text error-kode text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                    <small class="error-text error-nama text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="harga">Harga Produk</label>
                    <input type="number" class="form-control" id="harga" name="harga" required>
                    <small class="error-text error-harga text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi Produk</label>
                    <textarea name="deskripsi" id="deskripsi" name="deskripsi" rows="3" class="form-control"
                        required></textarea>
                    <small class="error-text error-deskripsi text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="foto_produk">Foto produk</label>
                    <input type="file" class="form-control" id="foto_produk" name="foto_produk">
                    <small class="error-text error-foto_produk text-danger"></small>
                </div>

                <button type="submit" class="btn btn-primary tombol-submit">Submit</button>

                <?= form_close(); ?>
            </div>
        </div>
    </div>


    <div id="baseurl" data-baseurl="<?= base_url(); ?>"></div>
    <div class="" id="flasher-wrapper">
        <?= $this->session->flashdata('flash'); ?>
        <?= $this->session->flashdata('flash2'); ?>
    </div>

    <script src="<?= base_url("vendor/bootstrap/4.4.1/js/bootstrap.min.js"); ?>"></script>
    <script src="<?= base_url("vendor/sweetalert2/sweetalert2.all.min.js"); ?>"></script>
    <script src="<?= base_url("vendor/fontawesome-free-5.9.0-web/js/all.min.js"); ?>"></script>
    <script src="<?= base_url("vendor/select2-4.0.6-rc.1/dist/js/select2.full.min.js"); ?>"></script>

    <script src="<?= base_url("assets/js/custom.js"); ?>"></script>

    <script>
    $(function() {
        $(".loader").hide();

        // event klkik tombol submit 
        $(".tombol-submit").on("click", function() {
            event.preventDefault();
            $(".loader").show();
            let form = $("#form_input_produk");
            let dataInput = new FormData(form[0]);
            $.ajax({
                url: baseurl + "Admin/ex_input_produk",
                dataType: 'json',
                method: 'post',
                data: dataInput,
                processData: false,
                contentType: false,
                error: function(res) {
                    $(".loader").hide();
                    console.log('error');
                    console.log(res);
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Terjadi kesalahan!',
                        showConfirmButton: false,
                        timer: 800
                    });
                },
                success: function(res) {
                    console.log('success');
                    console.log(res);
                    // return false;
                    $("[name='" + res.name + "']").val(res.hash);
                    $(".error-text").html("");
                    if (res.status == "hold") {
                        $(".loader").hide();
                        $.each(res.data_error, function(key, value) {
                            $('.error-' + key).html(value);
                        });
                        Swal.fire({
                            position: 'center',
                            icon: 'warning',
                            title: 'Cek inputan anda!',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        $(".loader").hide();
                        flasher($.parseHTML("<div>" + res.alert + "</div>"));
                        // window.location = baseurl + "pasangan-calon";
                    }
                }
            });
        });

        function disableInput() {
            $(".container-fluid").find("input").attr("required", false);
            $(".container-fluid").find("select").attr("required", false);
            $(".container-fluid").find("textarea").attr("required", false);
        }

        $("#id_toko").select2({
            placeholder: "Pilih toko",
            width: "100%"
        });



    });
    </script>
</body>

</html>