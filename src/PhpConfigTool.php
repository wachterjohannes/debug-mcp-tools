<?php

declare(strict_types=1);

namespace Wachterjohannes\DebugMcp\Tools;

use Mcp\Capability\Attribute\McpTool;

class PhpConfigTool
{
    #[McpTool(
        name: 'php_config',
        description: 'Inspect PHP configuration and environment'
    )]
    public function execute(string $section = 'general'): array
    {
        $validSections = ['general', 'extensions', 'paths', 'all'];
        if (! in_array($section, $validSections, true)) {
            return [
                'error' => 'Invalid section',
                'details' => sprintf(
                    'Section "%s" is not valid. Use one of: %s',
                    $section,
                    implode(', ', $validSections)
                ),
            ];
        }

        $result = [];

        if ($section === 'general' || $section === 'all') {
            $result = array_merge($result, $this->getGeneralInfo());
        }

        if ($section === 'extensions' || $section === 'all') {
            $result = array_merge($result, $this->getExtensionsInfo());
        }

        if ($section === 'paths' || $section === 'all') {
            $result = array_merge($result, $this->getPathsInfo());
        }

        return $result;
    }

    private function getGeneralInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'zend_version' => zend_version(),
            'memory_limit' => ini_get('memory_limit') ?: 'unknown',
            'max_execution_time' => ini_get('max_execution_time') ?: 'unknown',
            'error_reporting' => $this->getErrorReportingString(),
        ];
    }

    private function getExtensionsInfo(): array
    {
        $extensions = get_loaded_extensions();
        sort($extensions);

        return [
            'extensions' => $extensions,
            'count' => count($extensions),
        ];
    }

    private function getPathsInfo(): array
    {
        return [
            'include_path' => get_include_path(),
            'config_file_path' => php_ini_loaded_file() ?: 'none',
            'config_file_scan_dir' => php_ini_scanned_files() ?: 'none',
        ];
    }

    private function getErrorReportingString(): string
    {
        $level = error_reporting();

        $levels = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        if ($level === E_ALL) {
            return 'E_ALL';
        }

        $active = [];
        foreach ($levels as $value => $name) {
            if ($level & $value) {
                $active[] = $name;
            }
        }

        return ! empty($active) ? implode(' | ', $active) : (string) $level;
    }
}
