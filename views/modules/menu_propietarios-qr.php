<?php 
require "services/generador_qrcode.php";
?>
<div class="titulo-boton">
    <h1 class="titulo-modulo">Código QR</h1>
</div>
<div class="caja text-center">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <?php 
                $qr = QRCodeGenerator::generateQRCode($_SESSION['id']); 
                $qrPath = $url . $qr;
            ?>
            <a  style="text-decoration: none;" href="<?=$url.'menu_propietarios/'.$_SESSION['id']?>">
                <img src="<?=$qrPath?>" alt="Código QR" class="img-fluid" style="width: 100%; height: auto; cursor: pointer;" title="Haz clic para descargar el código QR">
            </a>
        </div>
        <div class="col-md-12">
            <a type="button" class="btn btn-primary" href="<?=$qrPath?>" download="menu_qr.png">Descargar</a>
        </div>
    </div>
</div>
