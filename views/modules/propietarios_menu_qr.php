<?php
require "services/generador_qrcode.php";

$cafeteria = GeneralController::verificarCafeteriaContoller($action[1], $_SESSION['id']);
if ($cafeteria) {
    $qr = QRCodeGenerator::generateQRCode($action[1]);
    $qrPath = $url . $qr;
?>
    <div class="titulo-boton">
        <h1 class="titulo-modulo">Código QR</h1>
    </div>
    <div class="caja text-center">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <a style="text-decoration: none;" href="<?= $url . 'cafeterias_menu/' . $action[1]?>">
                    <img src="<?= $qrPath ?>" alt="Código QR" class="img-fluid" style="width: 100%; height: auto; cursor: pointer;" title="Haz clic para descargar el código QR">
                </a>
            </div>
            <div class="col-md-12">
                <a type="button" class="btn btn-primary" href="<?= $qrPath ?>" download="menu_qr.png">Descargar</a>
            </div>
        </div>
    </div>
<?php
} else {
    include '404.php';
}
?>