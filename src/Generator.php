<?php

declare(strict_types=1);

namespace PluginGenerator;

class Generator
{
    private string $templateDir;
    private array $replacements = [];
    
    public function __construct(string $templateDir)
    {
        $this->templateDir = rtrim($templateDir, '/\\');
    }
    
    public function setReplacements(array $data): void
    {
        $pluginSlug = $data['plugin_slug'];
        $pluginNamespace = $data['plugin_namespace'];
        $vendorNamespace = $data['vendor_namespace'];
        $vendorSlug = strtolower($vendorNamespace);
        
        // Create underscore version for JS variable names
        $pluginSlugUnderscore = str_replace('-', '_', $pluginSlug);
        
        // Mapowanie placeholderów na nowe wartości
        $this->replacements = [
            // Namespace replacements (order matters - more specific first)
            'Pluginboilerplatevendor\\Pluginboilerplate' => $vendorNamespace . '\\' . $pluginNamespace,
            'Pluginboilerplatevendor' => $vendorNamespace,
            'Pluginboilerplate' => $pluginNamespace,
            
            // Slug/lowercase replacements
            'pluginboilerplatevendor/pluginboilerplate' => $vendorSlug . '/' . $pluginSlug,
            'pluginboilerplatevendor' => $vendorSlug,
            // JS variable names (underscore version) - must come before regular slug replacement
            'pluginboilerplate_vars' => $pluginSlugUnderscore . '_vars',
            'pluginboilerplate' => $pluginSlug,
            
            // Plugin header replacements
            'Pluginboilerplate__description' => $data['plugin_description'] ?? 'Plugin description',
        ];
        
        // Store data for header generation
        $this->pluginData = $data;
    }
    
    private array $pluginData = [];
    
    public function generate(): array
    {
        $files = [];
        $this->processDirectory($this->templateDir, '', $files);
        return $files;
    }
    
    private function processDirectory(string $dir, string $relativePath, array &$files): void
    {
        $items = scandir($dir);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            // Skip files we don't want in the output
            if (in_array($item, ['.php-cs-fixer.dist.php', '.php-cs-fixer.cache', '.gitignore'])) {
                continue;
            }
            
            $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
            $itemRelativePath = $relativePath ? $relativePath . '/' . $item : $item;
            
            if (is_dir($fullPath)) {
                $this->processDirectory($fullPath, $itemRelativePath, $files);
            } else {
                $content = file_get_contents($fullPath);
                $newContent = $this->processContent($content, $item);
                $newFileName = $this->processFileName($item);
                $newRelativePath = $relativePath ? $relativePath . '/' . $newFileName : $newFileName;
                
                $files[$newRelativePath] = $newContent;
            }
        }
    }
    
    private function processContent(string $content, string $fileName): string
    {
        // Only process text files
        $textExtensions = ['php', 'json', 'js', 'css', 'txt', 'md'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $textExtensions)) {
            return $content;
        }
        
        // Special handling for main plugin file
        if ($fileName === 'pluginboilerplate.php') {
            $content = $this->generatePluginHeader() . $this->getPluginFileBody($content);
        }
        
        // Apply all replacements
        foreach ($this->replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        return $content;
    }
    
    private function generatePluginHeader(): string
    {
        $data = $this->pluginData;
        
        $header = "<?php\n\n";
        $header .= "/*\n";
        $header .= " * Plugin Name: " . ($data['plugin_name'] ?? 'My Plugin') . "\n";
        
        if (!empty($data['plugin_uri'])) {
            $header .= " * Plugin URI: " . $data['plugin_uri'] . "\n";
        }
        
        $header .= " * Description: " . ($data['plugin_description'] ?? 'Plugin description') . "\n";
        $header .= " * Version: " . ($data['version'] ?? '1.0.0') . "\n";
        $header .= " * Requires PHP: " . ($data['requires_php'] ?? '8.1') . "\n";
        $header .= " * Author: " . ($data['author_name'] ?? 'Author') . "\n";
        
        if (!empty($data['author_uri'])) {
            $header .= " * Author URI: " . $data['author_uri'] . "\n";
        }
        
        $header .= " * Text Domain: " . ($data['text_domain'] ?? $data['plugin_slug']) . "\n";
        $header .= " */\n";
        
        return $header;
    }
    
    private function getPluginFileBody(string $content): string
    {
        // Remove original header (everything before declare(strict_types=1);)
        $pos = strpos($content, 'declare(strict_types=1);');
        if ($pos !== false) {
            return "\n" . substr($content, $pos);
        }
        
        // Fallback: remove everything until first use statement
        $pos = strpos($content, 'use ');
        if ($pos !== false) {
            return "\ndeclare(strict_types=1);\n\n" . substr($content, $pos);
        }
        
        return $content;
    }
    
    private function processFileName(string $fileName): string
    {
        // Rename main plugin file
        if ($fileName === 'pluginboilerplate.php') {
            return $this->pluginData['plugin_slug'] . '.php';
        }
        
        return $fileName;
    }
}
