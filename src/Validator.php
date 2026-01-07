<?php

declare(strict_types=1);

namespace PluginGenerator;

class Validator
{
    private array $errors = [];
    
    // Field length limits
    private const LIMITS = [
        'plugin_name' => 100,
        'plugin_slug' => 50,
        'text_domain' => 50,
        'plugin_namespace' => 50,
        'vendor_namespace' => 50,
        'author_name' => 100,
        'author_uri' => 200,
        'plugin_uri' => 200,
        'plugin_description' => 500,
        'version' => 20,
        'requires_php' => 10,
    ];
    
    public function validate(array $data): bool
    {
        $this->errors = [];
        
        // Length validation for all fields
        foreach (self::LIMITS as $field => $maxLength) {
            $this->validateLength($data, $field, $maxLength);
        }
        
        // Required fields
        $this->validateRequired($data, 'plugin_name', 'Nazwa wtyczki');
        $this->validateRequired($data, 'plugin_slug', 'Slug wtyczki');
        $this->validateRequired($data, 'text_domain', 'Text Domain');
        $this->validateRequired($data, 'plugin_namespace', 'Namespace wtyczki');
        $this->validateRequired($data, 'vendor_namespace', 'Vendor Namespace');
        $this->validateRequired($data, 'author_name', 'Autor');
        
        // Format validation
        $this->validateSlug($data, 'plugin_slug', 'Slug wtyczki');
        $this->validateSlug($data, 'text_domain', 'Text Domain');
        $this->validateNamespace($data, 'plugin_namespace', 'Namespace wtyczki');
        $this->validateNamespace($data, 'vendor_namespace', 'Vendor Namespace');
        
        // Optional URL validation
        $this->validateUrl($data, 'plugin_uri', 'Strona wtyczki');
        $this->validateUrl($data, 'author_uri', 'Strona autora');
        
        // Version validation
        $this->validateVersion($data, 'version', 'Wersja');
        
        // Requires PHP validation
        $this->validateRequiresPhp($data);
        
        return empty($this->errors);
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    public function getErrorsAsString(): string
    {
        return implode("\n", $this->errors);
    }
    
    private function validateRequired(array $data, string $field, string $label): void
    {
        if (empty($data[$field]) || trim($data[$field]) === '') {
            $this->errors[] = "Pole \"{$label}\" jest wymagane.";
        }
    }
    
    private function validateSlug(array $data, string $field, string $label): void
    {
        if (!empty($data[$field]) && !preg_match('/^[a-z0-9-]+$/', $data[$field])) {
            $this->errors[] = "Pole \"{$label}\" może zawierać tylko małe litery, cyfry i myślniki.";
        }
    }
    
    private function validateNamespace(array $data, string $field, string $label): void
    {
        if (!empty($data[$field]) && !preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $data[$field])) {
            $this->errors[] = "Pole \"{$label}\" musi zaczynać się od litery i zawierać tylko litery i cyfry (PascalCase).";
        }
    }
    
    private function validateUrl(array $data, string $field, string $label): void
    {
        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[] = "Pole \"{$label}\" musi być poprawnym adresem URL.";
        }
    }
    
    private function validateVersion(array $data, string $field, string $label): void
    {
        if (!empty($data[$field]) && !preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/', $data[$field])) {
            $this->errors[] = "Pole \"{$label}\" musi być w formacie X.Y.Z (np. 1.0.0).";
        }
    }
    
    private function validateLength(array $data, string $field, int $maxLength): void
    {
        if (!empty($data[$field]) && mb_strlen($data[$field]) > $maxLength) {
            $this->errors[] = "Pole \"{$field}\" może mieć maksymalnie {$maxLength} znaków.";
        }
    }
    
    private function validateRequiresPhp(array $data): void
    {
        $allowed = ['8.1', '8.2', '8.3', '8.4'];
        if (!empty($data['requires_php']) && !in_array($data['requires_php'], $allowed, true)) {
            $this->errors[] = "Nieprawidłowa wersja PHP.";
        }
    }
    
    /**
     * Sanitize input data
     */
    public function sanitize(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(strip_tags($value));
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        // Set defaults
        if (empty($sanitized['version'])) {
            $sanitized['version'] = '1.0.0';
        }
        
        if (empty($sanitized['requires_php'])) {
            $sanitized['requires_php'] = '8.1';
        }
        
        if (empty($sanitized['plugin_description'])) {
            $sanitized['plugin_description'] = 'Plugin description';
        }
        
        return $sanitized;
    }
}
