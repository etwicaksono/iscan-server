<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="<?= base_url("vendor/bootstrap/4.4.1/css/bootstrap.min.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("vendor/sweetalert2/sweetalert2.min.css"); ?>">
    <link rel="stylesheet" href="<?= base_url("vendor/fontawesome-free-5.9.0-web/css/all.min.css"); ?>">

    <script src="<?= base_url("assets/js/jquery-3.4.1.min.js"); ?>"></script>
</head>

<body>


    <div class="" id="flasher-wrapper">
        <?= $this->session->flashdata('flash'); ?>
        <?= $this->session->flashdata('flash2'); ?>
    </div>

    <script src="<?= base_url("vendor/bootstrap/4.4.1/js/bootstrap.min.js"); ?>"></script>
    <script src="<?= base_url("vendor/sweetalert2/sweetalert2.all.min.js"); ?>"></script>
    <script src="<?= base_url("vendor/fontawesome-free-5.9.0-web/js/all.min.js"); ?>"></script>

    <script src="<?= base_url("assets/js/custom.js"); ?>"></script>
</body>

</html>