<?php

declare(strict_types=1);

namespace PluginGenerator;

class RateLimiter
{
    private string $storageDir;
    private int $maxRequests;
    private int $timeWindow;
    
    /**
     * @param string $storageDir Directory to store rate limit data
     * @param int $maxRequests Maximum requests allowed in time window
     * @param int $timeWindow Time window in seconds
     */
    public function __construct(
        string $storageDir,
        int $maxRequests = 10,
        int $timeWindow = 60
    ) {
        $this->storageDir = rtrim($storageDir, '/\\');
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        
        // Create storage directory if it doesn't exist
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }
    
    /**
     * Check if request is allowed for given identifier (IP address)
     */
    public function isAllowed(string $identifier): bool
    {
        $this->cleanup();
        
        $filename = $this->getFilename($identifier);
        $requests = $this->getRequests($filename);
        
        // Filter requests within time window
        $cutoff = time() - $this->timeWindow;
        $requests = array_filter($requests, fn($time) => $time > $cutoff);
        
        if (count($requests) >= $this->maxRequests) {
            return false;
        }
        
        // Add current request
        $requests[] = time();
        $this->saveRequests($filename, $requests);
        
        return true;
    }
    
    /**
     * Get remaining requests for identifier
     */
    public function getRemainingRequests(string $identifier): int
    {
        $filename = $this->getFilename($identifier);
        $requests = $this->getRequests($filename);
        
        $cutoff = time() - $this->timeWindow;
        $requests = array_filter($requests, fn($time) => $time > $cutoff);
        
        return max(0, $this->maxRequests - count($requests));
    }
    
    /**
     * Get seconds until rate limit resets
     */
    public function getSecondsUntilReset(string $identifier): int
    {
        $filename = $this->getFilename($identifier);
        $requests = $this->getRequests($filename);
        
        if (empty($requests)) {
            return 0;
        }
        
        $oldestRequest = min($requests);
        $resetTime = $oldestRequest + $this->timeWindow;
        
        return max(0, $resetTime - time());
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIp(): string
    {
        // Check for forwarded IP (behind proxy/load balancer)
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // X-Forwarded-For may contain multiple IPs, get the first one
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    private function getFilename(string $identifier): string
    {
        // Hash identifier to create safe filename
        return $this->storageDir . '/' . md5($identifier) . '.json';
    }
    
    private function getRequests(string $filename): array
    {
        if (!file_exists($filename)) {
            return [];
        }
        
        $content = file_get_contents($filename);
        $data = json_decode($content, true);
        
        return is_array($data) ? $data : [];
    }
    
    private function saveRequests(string $filename, array $requests): void
    {
        file_put_contents($filename, json_encode(array_values($requests)));
    }
    
    /**
     * Clean up old rate limit files
     */
    private function cleanup(): void
    {
        // Only run cleanup occasionally (1% chance)
        if (rand(1, 100) > 1) {
            return;
        }
        
        $files = glob($this->storageDir . '/*.json');
        $cutoff = time() - ($this->timeWindow * 2);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
}
