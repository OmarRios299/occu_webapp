<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\MailerSend;

final class PinMailerService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $fromEmail,
        private readonly string $fromName,
    ) {}

    public function sendPin(string $toEmail, string $pin): bool
    {
        $toName = explode('@', $toEmail)[0] ?: 'Usuario';

        $subject = 'OCCU - Confirmación de Registro';
        $html = $this->renderHtml($pin);

        try {
            $mailersend = new MailerSend(['api_key' => $this->apiKey]);
            $recipients = [new Recipient($toEmail, $toName)];
            $params = (new EmailParams())
                ->setFrom($this->fromEmail)
                ->setFromName($this->fromName)
                ->setRecipients($recipients)
                ->setSubject($subject)
                ->setHtml($html)
                ->setText('Tu código de verificación es: ' . $pin);

            $mailersend->email->send($params);
            return true;
        } catch (\Throwable $e) {
            error_log('PinMailerService error: ' . $e->getMessage());
            return false;
        }
    }

    private function renderHtml(string $pin): string
    {
        $fecha = date('Y-m-d');

        return '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");
            .correo-contenido * { font-family: "Ubuntu", "Arial", sans-serif; }
        </style>
        <div class="correo-contenido" style="background-color: #E8EBF4; padding: 40px 20px; text-align: center;">
            <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                <div style="background-color: #F57C46; padding: 20px;">
                    <h1 style="color: white; margin: 0;">¡Bienvenido a OCCU!</h1>
                </div>
                <div style="padding: 30px;">
                    <p style="font-size: 18px; color: #333;">Gracias por registrarte.</p>
                    <p style="font-size: 16px; color: #555;">Aquí tienes tu código de verificación:</p>
                    <p style="font-size: 32px; font-weight: bold; color: #F57C46; margin: 20px 0;">' . htmlspecialchars($pin) . '</p>
                    <p style="font-size: 14px; color: #999;">Fecha: ' . $fecha . '</p>
                </div>
                <div style="background-color: #F9F4F1; padding: 15px;">
                    <p style="font-size: 12px; color: #666;">Este correo fue generado automáticamente por la plataforma OCCU.</p>
                </div>
            </div>
        </div>';
    }
}

