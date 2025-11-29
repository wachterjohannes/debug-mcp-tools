# debug-mcp-tools - Tool Provider Package

## Project Overview

This package provides debugging and development tools for the debug-mcp MCP server ecosystem. It demonstrates the extension package pattern and serves as a reference implementation for creating MCP tools.

**Role in Ecosystem**: Extension package providing debugging utilities

**Key Responsibility**: Tool implementations for time and PHP environment inspection

## Architecture

### Component Structure

```
debug-mcp-tools
├── ClockTool - Current time with formatting
└── PhpConfigTool - PHP environment inspection
```

### Tool Implementations

Each tool is a standalone class with MCP attributes:

```php
use Mcp\Capability\Attribute\McpTool;

class ToolName
{
    #[McpTool(
        name: 'tool_name',
        description: 'What this tool does'
    )]
    public function execute(/* parameters */): array
    {
        // Tool logic
        return ['result_key' => 'result_value'];
    }
}
```

## Tool Specifications

### ClockTool

**Purpose**: Provide current time with timezone and format customization

**Use Cases**:
- Timestamp generation for logs or reports
- Timezone conversion verification
- Date/time formatting examples

**Implementation Notes**:
- Uses PHP's `DateTime` and `DateTimeZone`
- Validates timezone against `DateTimeZone::listIdentifiers()`
- Supports all PHP date format characters
- Returns ISO 8601 format by default when using 'c' format

**Parameters**:
- `format` (string, optional): PHP date() format string
  - Default: 'Y-m-d H:i:s'
  - Examples: 'c' (ISO 8601), 'U' (Unix timestamp), 'Y-m-d', 'H:i:s'
- `timezone` (string, optional): Valid timezone identifier
  - Default: 'UTC'
  - Examples: 'America/New_York', 'Europe/London', 'Asia/Tokyo'

**Return Format**:
```json
{
  "time": "2024-11-29 14:30:45"
}
```

### PhpConfigTool

**Purpose**: Inspect PHP configuration and environment

**Use Cases**:
- Debugging environment-specific issues
- Verifying extension availability
- Checking memory and execution limits
- Finding PHP configuration file paths

**Implementation Notes**:
- Uses `phpversion()`, `ini_get()`, `get_loaded_extensions()`
- Filters sensitive information (paths can be exposed, but not credentials)
- Organized into logical sections for clarity
- Returns structured data for easy parsing

**Parameters**:
- `section` (string, optional): Information category
  - `general` (default): Version, memory, execution time
  - `extensions`: Loaded extensions list
  - `paths`: Include paths, config file location
  - `all`: Complete information

**Return Format (general)**:
```json
{
  "php_version": "8.2.12",
  "zend_version": "4.2.12",
  "memory_limit": "256M",
  "max_execution_time": "30",
  "error_reporting": "E_ALL"
}
```

**Return Format (extensions)**:
```json
{
  "extensions": [
    "Core", "date", "pcre", "SPL", "json", "mbstring", "pdo", ...
  ],
  "count": 45
}
```

## Development Guidelines

### Adding New Tools

1. **Create Tool Class**:
   ```php
   <?php
   namespace Wachterjohannes\DebugMcp\Tools;

   use Mcp\Capability\Attribute\McpTool;

   class NewTool
   {
       #[McpTool(name: 'new_tool', description: 'Tool description')]
       public function execute(string $param): array
       {
           // Implementation
           return ['key' => 'value'];
       }
   }
   ```

2. **Register in composer.json**:
   ```json
   {
     "extra": {
       "wachterjohannes/debug-mcp": {
         "classes": [
           "Wachterjohannes\\DebugMcp\\Tools\\NewTool"
         ]
       }
     }
   }
   ```

3. **Document in README.md**:
   - Tool name and purpose
   - Parameters with types and defaults
   - Return format example
   - Usage example

### Code Style

- **PSR-12**: Follow PSR-12 coding standards
- **Type Hints**: All parameters and returns typed
- **Attributes**: Use `#[McpTool]` for declaration
- **Return Arrays**: Always return associative arrays for JSON serialization
- **Validation**: Validate inputs before processing
- **Error Handling**: Return error information in result array when appropriate

### Testing Approach

**Manual Testing**:
1. Install package in debug-mcp instance
2. Start MCP server
3. Send JSON-RPC tool/call message
4. Verify response format and content

**Integration Testing**:
1. Configure Claude Desktop with debug-mcp
2. Use tool via natural language
3. Verify correct execution and response

## Integration Points

### With debug-mcp Server

The server discovers tools through:
1. Reading `vendor/composer/installed.json`
2. Finding this package's `extra.wachterjohannes/debug-mcp.classes`
3. Instantiating the tool classes
4. SDK discovers methods via `#[McpTool]` attributes

### With MCP SDK

Uses official `modelcontextprotocol/php-sdk` attributes:

```php
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'tool_name',           // Required: Tool identifier
    description: 'Description'    // Required: Tool purpose
)]
```

The SDK handles:
- Schema generation from method signatures
- Parameter validation
- JSON-RPC request/response formatting
- Error handling

## Key Implementation Patterns

### Parameter Handling

```php
public function execute(
    string $requiredParam,
    string $optionalParam = 'default',
    ?string $nullableParam = null
): array
```

- Required parameters: No default value
- Optional parameters: Default value provided
- Nullable parameters: Use `?type` and `null` default

### Return Format

Always return associative arrays for JSON encoding:

```php
return [
    'result_key' => 'value',
    'another_key' => ['nested', 'array'],
    'status' => 'success'
];
```

### Error Handling

Return errors as part of the result:

```php
if ($invalid) {
    return [
        'error' => 'Invalid parameter',
        'details' => 'Parameter X must be Y'
    ];
}
```

## SDK Attribute Reference

### #[McpTool]

**Required Properties**:
- `name` (string): Unique tool identifier (lowercase, underscores)
- `description` (string): Human-readable tool purpose

**Optional Properties**:
- Check SDK documentation for additional options

**Usage**:
```php
#[McpTool(
    name: 'my_tool',
    description: 'Does something useful'
)]
public function execute(...): array
```

## Quick Implementation Checklist

- [ ] `src/ClockTool.php` - Time tool implementation
- [ ] `src/PhpConfigTool.php` - PHP config tool implementation
- [ ] `composer.json` - Package definition with extra config
- [ ] `README.md` - User documentation
- [ ] `.php-cs-fixer.php` - Code style configuration
- [ ] Test installation in debug-mcp
- [ ] Verify discovery and tool execution

## Future Tool Ideas

Potential tools to add:

1. **FileStatTool**: Get file/directory statistics
2. **ProcessInfoTool**: Current PHP process information
3. **OpCacheTool**: OPcache status and statistics
4. **ComposerTool**: Installed packages and versions
5. **GitTool**: Repository information and status

## Repository Information

- **GitHub**: https://github.com/wachterjohannes/debug-mcp-tools
- **Packagist**: (publish after implementation)
- **License**: MIT
