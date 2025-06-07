<?php

require 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeGenerator
{
    public static function generateQRCode($id_cafeteria)
    {
        try {
            $url = TemplateController::obtenerUrlController();

            // Generar el enlace completo
            $fullUrl = trim($url . 'cafeterias_menu/' . $id_cafeteria);

            // Validar y limpiar la URL
            $fullUrl = filter_var($fullUrl, FILTER_SANITIZE_URL);

            if (!filter_var($fullUrl, FILTER_VALIDATE_URL)) {
                throw new Exception("La URL no es válida: $fullUrl");
            }

            // Configuración del QR
            $options = new QROptions([
                'version' => 5,
                'outputType' => QRCode::OUTPUT_IMAGE_PNG, // Especificar que sea un PNG
                'eccLevel' => QRCode::ECC_M,
                'scale' => 5,
                'outputBase64' => false, // Desactivar salida en Base64
            ]);

            // Crear instancia del generador QR
            $qrcode = new QRCode($options);

            // Generar la imagen del QR
            $qrImage = $qrcode->render($fullUrl);

            // Verificar si el contenido es un PNG válido
            if (strpos($qrImage, "\x89PNG") !== 0) {
                throw new Exception("El contenido generado no es un PNG válido.");
            }

            // Definir la ruta donde se guardará el QR
            $outputPath = 'views/assets/img/cafeterias_qr';
            if (!is_dir($outputPath)) {
                mkdir($outputPath, 0777, true);
            }

            $fileName = $outputPath . '/qr_' . $id_cafeteria . '.png';

            // Guardar el archivo en la ruta correcta
            if (file_put_contents($fileName, $qrImage) === false) {
                throw new Exception("No se pudo guardar el archivo QR en: $fileName");
            }

            // Retornar la URL generada
            return $fileName;

        } catch (Exception $e) {
            return "Error al generar el código QR: " . $e->getMessage();
        }
    }
}
