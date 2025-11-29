# debug-mcp-tools

**⚠️ PROTOTYPE - FOR TESTING AND DISCUSSION PURPOSES ONLY**

---

Development and debugging tools for the debug-mcp MCP server.

## Purpose

Provides two essential debugging tools:
- **ClockTool**: Get current time with customizable format and timezone
- **PhpConfigTool**: Inspect PHP configuration, extensions, and environment

## Features

- Current time retrieval with timezone support
- PHP configuration inspection (version, extensions, paths, memory limits)
- Automatic discovery by debug-mcp server
- Attribute-based registration using PHP 8

## Installation

```bash
composer require wachterjohannes/debug-mcp-tools
```

The tools will be automatically discovered when debug-mcp server starts.

## Available Tools

### clock

Get the current time in a specified format and timezone.

**Parameters:**
- `format` (optional, default: 'Y-m-d H:i:s'): PHP date format string
- `timezone` (optional, default: 'UTC'): Valid timezone identifier

**Example:**
```json
{
  "jsonrpc": "2.0",
  "method": "tools/call",
  "params": {
    "name": "clock",
    "arguments": {
      "format": "c",
      "timezone": "Europe/Berlin"
    }
  },
  "id": 1
}
```

**Returns:**
```json
{
  "time": "2024-11-29T15:30:45+01:00"
}
```

### php_config

Get PHP configuration information about the development environment.

**Parameters:**
- `section` (optional, default: 'general'): One of:
  - `general`: PHP version, memory limit, max execution time
  - `extensions`: List of loaded PHP extensions
  - `paths`: Include paths and configuration file locations
  - `all`: All information combined

**Example:**
```json
{
  "jsonrpc": "2.0",
  "method": "tools/call",
  "params": {
    "name": "php_config",
    "arguments": {
      "section": "general"
    }
  },
  "id": 1
}
```

**Returns:**
```json
{
  "php_version": "8.2.12",
  "memory_limit": "256M",
  "max_execution_time": "30",
  "zend_version": "4.2.12"
}
```

## Registration

Tools are registered via composer.json extra configuration:

```json
{
  "extra": {
    "wachterjohannes/debug-mcp": {
      "classes": [
        "Wachterjohannes\\DebugMcp\\Tools\\ClockTool",
        "Wachterjohannes\\DebugMcp\\Tools\\PhpConfigTool"
      ]
    }
  }
}
```

The debug-mcp server scans installed packages for this configuration and automatically loads the tools.

## Adding New Tools

To add a new tool to this package:

1. Create a class in `src/` with the tool logic
2. Add `#[McpTool]` attribute from the SDK
3. Add the class name to `composer.json` extra config
4. Update this README with tool documentation

Example tool structure:

```php
<?php
namespace Wachterjohannes\DebugMcp\Tools;

use PhpMcp\Server\Attributes\McpTool;

class MyNewTool
{
    #[McpTool(
        name: 'my_new_tool',
        description: 'Description of what this tool does'
    )]
    public function execute(string $param): array
    {
        return ['result' => 'value'];
    }
}
```

## Development

### Code Quality

Format code before committing:

```bash
composer cs-fix
```

### Testing Tools

Test individual tools by installing into a debug-mcp instance and using Claude Desktop or direct JSON-RPC messages.

## Requirements

- PHP 8.1 or higher
- modelcontextprotocol/php-sdk
- wachterjohannes/debug-mcp (for testing)

## Repository

GitHub: https://github.com/wachterjohannes/debug-mcp-tools

## License

MIT
