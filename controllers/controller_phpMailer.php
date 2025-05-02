<?php

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

require_once __DIR__ . '/../vendor/autoload.php';

class MailController
{
    static public function enviarCorreoRegistro($datos)
    {
        date_default_timezone_set("America/Tijuana");
        setlocale(LC_TIME, 'spanish');

        $apiKey = 'mlsn.';
        $fromEmail = 'noreply@test-ywj2lpnnk0mg7oqz.mlsender.net';
        $fromName = 'OCCU';
        $toEmail = $datos['correo'];
        $toName = explode('@', $toEmail)[0];
        $subject = 'OCCU - Confirmación de Registro';

        $htmlContent = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");
            .correo-contenido * {
                font-family: "Ubuntu", "Arial", sans-serif;
            }
        </style>
        <div style="background-color: #E8EBF4; padding: 40px 20px; text-align: center;">
            <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                <div style="background-color: #F57C46; padding: 20px;">
                    <h1 style="color: white; margin: 0;">¡Bienvenido a OCCU!</h1>
                </div>
                <div style="padding: 30px;">
                    <p style="font-size: 18px; color: #333;">Gracias por registrarte.</p>
                    <p style="font-size: 16px; color: #555;">Aquí tienes tu código de verificación:</p>
                    <p style="font-size: 32px; font-weight: bold; color: #F57C46; margin: 20px 0;">' . $datos['pin'] . '</p>
                    <p style="font-size: 14px; color: #999;">Fecha: ' . date('Y-m-d') . '</p>
                </div>
                <div style="background-color: #F9F4F1; padding: 15px;">
                    <p style="font-size: 12px; color: #666;">Este correo fue generado automáticamente por la plataforma OCCU.</p>
                </div>
            </div>
        </div>';


        try {
            $mailersend = new MailerSend(['api_key' => $apiKey]);

            $recipients = [new Recipient($toEmail, $toName)];

            $emailParams = (new EmailParams())
                ->setFrom($fromEmail)
                ->setFromName($fromName)
                ->setRecipients($recipients)
                ->setSubject($subject)
                ->setHtml($htmlContent)
                ->setText('Tu código de verificación es: ' . $datos['pin']);

            $mailersend->email->send($emailParams);
            return true;
        } catch (Exception $e) {
            error_log('Error enviando correo con MailerSend: ' . $e->getMessage());
            return false;
        }
    }
}
