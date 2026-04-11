<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use Throwable;

final class ImageUploadService
{
    /**
     * Sube una imagen al directorio especificado
     * Basado en la lógica de GeneralController::subirImagen del legacy
     * 
     * @param array $file Array $_FILES con el archivo a subir
     * @param string $directorio Nombre del directorio donde se guardará (ej: 'cafeterias')
     * @param string $nombre Nombre base para el archivo (sin extensión)
     * @return string Ruta relativa de la imagen subida o 'error_img' si falla
     */
    public function uploadImage(array $file, string $directorio, string $nombre): string
    {
        if (!isset($file['tmp_name']) || !isset($file['type'])) {
            return 'error_img';
        }

        $tipo = $file['type'];
        $tmpimg = $file['tmp_name'];

        // Mapear tipos MIME a extensiones
        $tiposPermitidos = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'image/svg+xml' => 'svg',
            'image/svg' => 'svg'
        ];

        // Obtener extensión según el tipo
        $ext = $tiposPermitidos[$tipo] ?? null;

        if (!$ext) {
            return 'invalido';
        }

        // Validar que el archivo temporal existe
        if (!file_exists($tmpimg)) {
            return 'error_img';
        }

        // Crear directorio si no existe
        $directorioPath = __DIR__ . "/../../../views/assets/img/$directorio";
        if (!is_dir($directorioPath)) {
            if (!mkdir($directorioPath, 0755, true)) {
                error_log("Error creando directorio: $directorioPath");
                return 'error_img';
            }
        }

        $aleatorio = mt_rand(100, 999);
        $nuevo_nombre = $aleatorio . '_' . $nombre . '.' . $ext;
        $ruta = $directorioPath . '/' . $nuevo_nombre;
        $ruta_produccion = "views/assets/img/$directorio/$nuevo_nombre";

        // Mover el archivo subido
        $move = @move_uploaded_file($tmpimg, $ruta);

        if (!$move) {
            return 'error_img';
        }

        // Si es SVG, no convertir a WebP, solo devolver la ruta
        if ($ext === 'svg') {
            return $ruta_produccion;
        }

        // Convertir a WebP (excepto si ya es WebP)
        if ($ext !== 'webp') {
            $rutaWebp = $this->convertirImagenWebp($ruta, true);
            if ($rutaWebp !== 'error_img') {
                return $rutaWebp;
            }
            // Si falla la conversión, devolver la imagen original
            return $ruta_produccion;
        }

        return $ruta_produccion;
    }

    /**
     * Convierte una imagen a formato WebP
     * Basado en GeneralController::convertirImagenWebp del legacy
     * 
     * @param string $imagePath Ruta completa de la imagen
     * @param bool $eliminar Si se debe eliminar la imagen original
     * @return string Ruta relativa de la imagen WebP o 'error_img' si falla
     */
    private function convertirImagenWebp(string $imagePath, bool $eliminar = false): string
    {
        $quality = 80;
        $im = null;
        $newImagePath = '';

        // Determinar el tipo real de imagen usando getimagesize
        $imageInfo = @getimagesize($imagePath);

        if ($imageInfo === false) {
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $mimeType = null;
        } else {
            $mimeType = $imageInfo['mime'];
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        }

        // PNG a WEBP
        if (($mimeType === 'image/png' || ($mimeType === null && $extension === 'png')) && function_exists("imagecreatefrompng")) {
            $im = @imagecreatefrompng($imagePath);
            if ($im !== false) {
                imagepalettetotruecolor($im);
                imagealphablending($im, true);
                imagesavealpha($im, true);
                $newImagePath = str_replace("." . $extension, ".webp", $imagePath);
            }
        }
        // JPEG a WEBP
        elseif (($mimeType === 'image/jpeg' || $mimeType === 'image/jpg' || ($mimeType === null && in_array($extension, ['jpg', 'jpeg']))) && function_exists("imagecreatefromjpeg")) {
            $im = @imagecreatefromjpeg($imagePath);
            if ($im !== false) {
                $newImagePath = str_replace("." . $extension, ".webp", $imagePath);
            }
        }
        // GIF a WEBP
        elseif (($mimeType === 'image/gif' || ($mimeType === null && $extension === 'gif')) && function_exists("imagecreatefromgif")) {
            $im = @imagecreatefromgif($imagePath);
            if ($im !== false) {
                imagepalettetotruecolor($im);
                $newImagePath = str_replace("." . $extension, ".webp", $imagePath);
            }
        }
        // BMP a WEBP
        elseif (($mimeType === 'image/bmp' || ($mimeType === null && $extension === 'bmp')) && function_exists("imagecreatefrombmp")) {
            $im = @imagecreatefrombmp($imagePath);
            if ($im !== false) {
                $newImagePath = str_replace("." . $extension, ".webp", $imagePath);
            }
        }

        if ($im === false || empty($newImagePath)) {
            return 'error_img';
        }

        // Convertir a WebP
        if (!function_exists("imagewebp")) {
            imagedestroy($im);
            return 'error_img';
        }

        $success = @imagewebp($im, $newImagePath, $quality);
        imagedestroy($im);

        if (!$success) {
            return 'error_img';
        }

        // Eliminar imagen original si se solicita
        if ($eliminar && file_exists($imagePath)) {
            @unlink($imagePath);
        }

        // Retornar ruta relativa (sin ../../)
        return str_replace(__DIR__ . '/../../../', '', $newImagePath);
    }

    /**
     * Elimina una imagen del servidor
     * 
     * @param string $rutaRelativa Ruta relativa de la imagen (ej: 'views/assets/img/cafeterias/123_imagen.jpg')
     * @return bool True si se eliminó correctamente
     */
    public function deleteImage(string $rutaRelativa): bool
    {
        // No eliminar la imagen por defecto
        if ($rutaRelativa === 'views/assets/img/cafeteria_default.png') {
            return false;
        }

        $rutaCompleta = __DIR__ . "/../../../$rutaRelativa";
        
        if (file_exists($rutaCompleta)) {
            return @unlink($rutaCompleta);
        }

        return false;
    }
}
