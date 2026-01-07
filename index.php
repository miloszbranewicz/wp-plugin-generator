<?php
declare(strict_types=1);
require_once __DIR__ . '/src/CsrfProtection.php';
$csrf = new PluginGenerator\CsrfProtection();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Plugin Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h3 mb-0">WordPress Plugin Generator</h1>
                        <p class="mb-0 small opacity-75">Wygeneruj szkielet wtyczki WordPress w kilka sekund</p>
                    </div>
                    <div class="card-body">
                        <form id="plugin-form" action="generate.php" method="POST">
                            <?php echo $csrf->getTokenField(); ?>
                            
                            <!-- Sekcja: Podstawowe informacje -->
                            <h5 class="border-bottom pb-2 mb-3">Podstawowe informacje</h5>
                            
                            <div class="mb-3">
                                <label for="plugin_name" class="form-label">Nazwa wtyczki <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="plugin_name" name="plugin_name" 
                                       placeholder="np. My Awesome Plugin" required maxlength="100">
                                <div class="form-text">Pełna nazwa wtyczki wyświetlana w panelu WordPress (max 100 znaków)</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="plugin_slug" class="form-label">Slug wtyczki <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="plugin_slug" name="plugin_slug" 
                                           placeholder="np. my-awesome-plugin" required pattern="[a-z0-9-]+" maxlength="50">
                                    <div class="form-text">Tylko małe litery, cyfry i myślniki (max 50)</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="text_domain" class="form-label">Text Domain <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="text_domain" name="text_domain" 
                                           placeholder="np. my-awesome-plugin" required pattern="[a-z0-9-]+" maxlength="50">
                                    <div class="form-text">Dla tłumaczeń (max 50 znaków)</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="plugin_namespace" class="form-label">Namespace wtyczki <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="plugin_namespace" name="plugin_namespace" 
                                           placeholder="np. MyAwesomePlugin" required pattern="[A-Za-z][A-Za-z0-9]*" maxlength="50">
                                    <div class="form-text">PascalCase, np. MyAwesomePlugin (max 50)</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="vendor_namespace" class="form-label">Vendor Namespace <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="vendor_namespace" name="vendor_namespace" 
                                           placeholder="np. MyCompany" required pattern="[A-Za-z][A-Za-z0-9]*" maxlength="50">
                                    <div class="form-text">Prefix namespace, np. nazwa firmy (max 50)</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="plugin_description" class="form-label">Opis wtyczki</label>
                                <textarea class="form-control" id="plugin_description" name="plugin_description" 
                                          rows="2" placeholder="Krótki opis funkcjonalności wtyczki..." maxlength="500"></textarea>
                                <div class="form-text">Max 500 znaków</div>
                            </div>
                            
                            <!-- Sekcja: Autor -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Informacje o autorze</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="author_name" class="form-label">Autor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="author_name" name="author_name" 
                                           placeholder="np. Jan Kowalski" required maxlength="100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="author_uri" class="form-label">Strona autora</label>
                                    <input type="url" class="form-control" id="author_uri" name="author_uri" 
                                           placeholder="https://example.com" maxlength="200">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="plugin_uri" class="form-label">Strona wtyczki</label>
                                <input type="url" class="form-control" id="plugin_uri" name="plugin_uri" 
                                       placeholder="https://example.com/my-plugin" maxlength="200">
                            </div>
                            
                            <!-- Sekcja: Wersje -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Wersje</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="version" class="form-label">Wersja wtyczki</label>
                                    <input type="text" class="form-control" id="version" name="version" 
                                           value="1.0.0" placeholder="1.0.0" pattern="[0-9]+\.[0-9]+\.[0-9]+" maxlength="20">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="requires_php" class="form-label">Wymagana wersja PHP</label>
                                    <select class="form-select" id="requires_php" name="requires_php">
                                        <option value="8.1" selected>8.1</option>
                                        <option value="8.2">8.2</option>
                                        <option value="8.3">8.3</option>
                                        <option value="8.4">8.4</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Przycisk -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-download me-2" viewBox="0 0 16 16">
                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                    </svg>
                                    Wygeneruj wtyczkę
                                </button>
                            </div>
                            
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center small">
                        Oparty na <a href="https://github.com/your-repo/turbo-sniffle" target="_blank">turbo-sniffle</a> boilerplate
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
