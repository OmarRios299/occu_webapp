<?php
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__.'/../controllers/controller_template.php';

$apiKey = TemplateController::obtenerKeyGoogle();
echo json_encode(['apiKey' => $apiKey]);
