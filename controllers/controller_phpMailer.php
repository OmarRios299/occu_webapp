<?php

require 'phpMailer/Exception.php';
require 'phpMailer/PHPMailer.php';
require 'phpMailer/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class MailController
{
    static public function enviarCorreoRegistro($datos)
    {
        date_default_timezone_set("America/Tijuana");
        setlocale(LC_TIME, 'spanish');

        $fromEmail = 'info@occu.app'; 
        $password = 'Fraterccino0216';
        $toEmail = $datos['correo']; 
        $subject = 'OCCU - Confirmación de Registro';

        $htmlContent = '
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");
                .correo-contenido * {
                    font-family: "Ubuntu", "Arial", sans-serif;
                    font-weight: 300;
                }
                .table {
                    margin-left: auto;
                    margin-right: auto;
                    background: #cccccc;
                }
                p {
                    font-size: 14pt;
                    color: #000;
                    margin: 0;
                }
                .contenido {
                    display: block;
                    width: 350px;
                    margin: 0 auto;
                }
                .color-principal { color: #35d2d2; }
                .color-gris { color: #c1c2c2; }
            </style>
            <div class="correo-contenido" style="text-align:center; margin: 0 auto; padding: 20px 40px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tr>
                        <td align="center">
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600">
                                <tbody>
                                    <tr>
                                        <td style="background-color:#006E9F; text-align:center;">
                                            <img src="" alt="" style="width:120px; height: auto; margin: 15px auto 0; display: block;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">
                                            <div style="background-color:#006E9F; padding:12px; text-align:center;">
                                                <p style="color: #fff;font-size:28px">¡Hola!</p>
                                                <p style="color: #fff">Recibiste un correo de OCCU.</p>
                                                <p style="color: #fff">Fecha: ' . date('Y-m-d') . ' </p>
                                                <p style="color: #fff">Código de verificación.</p>
                                                <p style="color: #fff">'.$datos['pin'].'</p>
                                            </div>
                                        </td>
                                    </tr>                         
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
                <br>
                <p style="font-size: 10pt; color: #35d2d2; text-transform: uppercase; letter-spacing: 3px; margin: 0 auto;"><b>OCCU</b></p>
                <br>
            </div>';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtpout.secureserver.net';
            $mail->SMTPAuth = true;
            $mail->Username = $fromEmail;
            $mail->Password = $password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($fromEmail, 'OCCU');
            $mail->addAddress($toEmail);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $htmlContent;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Error enviando correo: ' . $mail->ErrorInfo);
            return false;
        }
    }
}

// class Mailchimp
// {

//     /* ID */

//     static public function idMailchimpController()
//     {
//         // return 'PZvReTrN5HkooydW5zgP9Q'; //pruebas e-sol
//         return 'd86ada68d2a6e7829356c94567cddc0a-us12'; //pruebas cmv
//     }

//     /* End of ID */

//     static public function enviarCorreoRegistroController()
//     {
//         // Obtención de la clave API de Mailchimp y configuración inicial
//         $idMailChimp = Mailchimp::idMailchimpController();
//         date_default_timezone_set("America/Tijuana");
//         $url = TemplateController::obtenerUrlController();
//         setlocale(LC_TIME, 'spanish');
    
//         // Mensaje de correo electrónico
//         $message = [
//             "from_email" => "info@occu.app",
//             "subject" => "OCCU",
//             "html" => '
//                 <style>
//                     @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");
//                     .correo-contenido * {
//                         font-family: "Ubuntu", "Arial", sans-serif;
//                         font-weight: 300;
//                     }
//                     .table {
//                         margin-left: auto;
//                         margin-right: auto;
//                         background: #cccccc;
//                     }
//                     p {
//                         font-size: 14pt;
//                         color: #000;
//                         margin: 0;
//                     }
//                     .contenido {
//                         display: block;
//                         width: 350px;
//                         margin: 0 auto;
//                     }
//                     .color-principal { color: #35d2d2; }
//                     .color-gris { color: #c1c2c2; }
//                 </style>
//                 <div class="correo-contenido" style="text-align:center; margin: 0 auto; padding: 20px 40px;">
//                     <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
//                         <tr>
//                             <td align="center">
//                                 <table border="0" cellpadding="0" cellspacing="0" align="center" width="600">
//                                     <tbody>
//                                         <tr>
//                                             <td style="background-color:#006E9F; text-align:center;">
//                                                 <img src="" alt="" style="width:120px; height: auto;margin: 15px auto 0; display: block;">
//                                             </td>
//                                         </tr>
//                                         <tr>
//                                             <td style="text-align: center;">
//                                                 <div style="background-color:#006E9F; padding:12px; text-align:center;">
//                                                     <p style="color: #fff;font-size:28px">¡Hola!</p>
//                                                     <p style="color: #fff">Recibiste un correo de occu.</p>
//                                                     <p style="color: #fff">Fecha: ' . date('Y-m-d') . ' </p>
//                                                 </div>
//                                             </td>
//                                         </tr>                         
//                                     </tbody>
//                                 </table>
//                             </td>
//                         </tr>
//                     </table>
//                     <br>
//                     <p style="font-size: 10pt; color: #35d2d2; text-transform: uppercase; letter-spacing: 3px; margin: 0 auto;"><b>OCCU</b></p>
//                     <br>
//                 </div>',
//             "to" => [
//                 [
//                     "email" => "omarrios299@gmail.com",
//                     "type" => "to"
//                 ]
//             ]
//         ];
    
//         try {
//             // Envío del correo utilizando Mailchimp Transactional
//             $mailchimp = new MailchimpTransactional\ApiClient();
//             $mailchimp->setApiKey($idMailChimp);
//             $response = $mailchimp->messages->send(["message" => $message]);
//             $response = json_decode(json_encode($response), true);
    
//             // Verificar si el correo fue enviado correctamente
//             return ($response[0]["status"] === "sent");
//         } catch (Exception $e) {
//             // Manejo de errores: registro del error para seguimiento
//             error_log('Error enviando correo: ' . $e->getMessage());
//             return false;
//         }
//     }
    
// }
