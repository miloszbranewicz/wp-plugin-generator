<?php

declare(strict_types=1);

namespace PluginGenerator;

class CsrfProtection
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_LENGTH = 32;
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Generate a new CSRF token
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION[self::TOKEN_NAME] = $token;
        $_SESSION[self::TOKEN_NAME . '_time'] = time();
        
        return $token;
    }
    
    /**
     * Get current token or generate new one
     */
    public function getToken(): string
    {
        if (empty($_SESSION[self::TOKEN_NAME])) {
            return $this->generateToken();
        }
        
        // Regenerate token if older than 1 hour
        $tokenTime = $_SESSION[self::TOKEN_NAME . '_time'] ?? 0;
        if (time() - $tokenTime > 3600) {
            return $this->generateToken();
        }
        
        return $_SESSION[self::TOKEN_NAME];
    }
    
    /**
     * Validate submitted token
     */
    public function validateToken(?string $submittedToken): bool
    {
        if (empty($submittedToken) || empty($_SESSION[self::TOKEN_NAME])) {
            return false;
        }
        
        // Use hash_equals to prevent timing attacks
        $isValid = hash_equals($_SESSION[self::TOKEN_NAME], $submittedToken);
        
        // Regenerate token after validation (one-time use)
        if ($isValid) {
            $this->generateToken();
        }
        
        return $isValid;
    }
    
    /**
     * Get HTML input field with token
     */
    public function getTokenField(): string
    {
        $token = htmlspecialchars($this->getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . $token . '">';
    }
    
    /**
     * Get token field name
     */
    public static function getTokenName(): string
    {
        return self::TOKEN_NAME;
    }
}
