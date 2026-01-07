<?php

declare(strict_types=1);

namespace PluginGenerator;

use ZipArchive;
use RuntimeException;

class ZipCreator
{
    private string $pluginSlug;
    
    public function __construct(string $pluginSlug)
    {
        $this->pluginSlug = $pluginSlug;
    }
    
    /**
     * Create ZIP archive from files array
     * 
     * @param array $files Array of [relativePath => content]
     * @return string Path to created ZIP file
     */
    public function create(array $files): string
    {
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->pluginSlug . '_' . uniqid() . '.zip';
        
        $zip = new ZipArchive();
        $result = $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        if ($result !== true) {
            throw new RuntimeException('Could not create ZIP archive. Error code: ' . $result);
        }
        
        // Create root folder with plugin slug
        $rootFolder = $this->pluginSlug . '/';
        
        foreach ($files as $relativePath => $content) {
            $fullPath = $rootFolder . $relativePath;
            
            // Create directory structure
            $dir = dirname($fullPath);
            if ($dir !== '.' && $dir !== $this->pluginSlug) {
                $this->addEmptyDir($zip, $dir);
            }
            
            // Add file
            $zip->addFromString($fullPath, $content);
        }
        
        $zip->close();
        
        return $tempFile;
    }
    
    /**
     * Add empty directory to ZIP (creates all parent directories)
     */
    private function addEmptyDir(ZipArchive $zip, string $dir): void
    {
        $parts = explode('/', $dir);
        $current = '';
        
        foreach ($parts as $part) {
            $current .= $part . '/';
            // ZipArchive automatically handles duplicate directory entries
            $zip->addEmptyDir($current);
        }
    }
    
    /**
     * Send ZIP file to browser for download
     */
    public function sendToBrowser(string $zipPath): void
    {
        if (!file_exists($zipPath)) {
            throw new RuntimeException('ZIP file not found: ' . $zipPath);
        }
        
        $fileName = $this->pluginSlug . '.zip';
        $fileSize = filesize($zipPath);
        
        // Clear any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output file
        readfile($zipPath);
        
        // Clean up temp file
        unlink($zipPath);
        
        exit;
    }
}
