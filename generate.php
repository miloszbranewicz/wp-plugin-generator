<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Generator.php';
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/ZipCreator.php';
require_once __DIR__ . '/src/CsrfProtection.php';
require_once __DIR__ . '/src/RateLimiter.php';

use PluginGenerator\Generator;
use PluginGenerator\Validator;
use PluginGenerator\ZipCreator;
use PluginGenerator\CsrfProtection;
use PluginGenerator\RateLimiter;

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// CSRF Protection
$csrf = new CsrfProtection();
$submittedToken = $_POST['csrf_token'] ?? null;

if (!$csrf->validateToken($submittedToken)) {
    showError('Nieprawidłowy token bezpieczeństwa. Odśwież stronę i spróbuj ponownie.');
}

// Rate Limiting - max 10 requests per minute per IP
$rateLimitDir = __DIR__ . '/storage/rate_limits';
$rateLimiter = new RateLimiter($rateLimitDir, 10, 60);
$clientIp = RateLimiter::getClientIp();

if (!$rateLimiter->isAllowed($clientIp)) {
    $secondsLeft = $rateLimiter->getSecondsUntilReset($clientIp);
    showError("Zbyt wiele żądań. Spróbuj ponownie za {$secondsLeft} sekund.");
}

// Get and sanitize input data
$validator = new Validator();
$data = $validator->sanitize($_POST);

// Validate input
if (!$validator->validate($data)) {
    // Show errors
    showError($validator->getErrorsAsString());
}

// Check if ZIP extension is available
if (!extension_loaded('zip')) {
    showError('Rozszerzenie PHP ZIP nie jest zainstalowane na serwerze.');
}

// Generate plugin
try {
    $templateDir = __DIR__ . '/template';
    
    if (!is_dir($templateDir)) {
        showError('Katalog szablonu nie istnieje.');
    }
    
    $generator = new Generator($templateDir);
    $generator->setReplacements($data);
    $files = $generator->generate();
    
    if (empty($files)) {
        showError('Nie udało się wygenerować plików wtyczki.');
    }
    
    $zipCreator = new ZipCreator($data['plugin_slug']);
    $zipPath = $zipCreator->create($files);
    $zipCreator->sendToBrowser($zipPath);
    
} catch (Exception $e) {
    showError('Wystąpił błąd podczas generowania wtyczki: ' . $e->getMessage());
}

/**
 * Show error page and exit
 */
function showError(string $message): void
{
    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Błąd - WordPress Plugin Generator</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0">Wystąpił błąd</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger mb-3">
                                <?php echo nl2br(htmlspecialchars($message)); ?>
                            </div>
                            <a href="index.php" class="btn btn-primary">
                                &larr; Wróć do formularza
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
