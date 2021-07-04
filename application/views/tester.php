<html>

<body>
    <?= form_open(""); ?>
    <input type="text" name="barcode_text">
    <input type="submit" name="generate_barcode" value="GENERATE">
    <?= form_close(); ?>
</body>

</html>

<?php
if (isset($_POST['generate_barcode'])) {
    $text = $_POST['barcode_text'];
    echo "<img alt='testing' src='" . base_url("Publics/tester") . "?codetype=Code39&size=40&text=" . $text . "&print=true'/>";
}
?>