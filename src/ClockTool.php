<?php

declare(strict_types=1);

namespace Wachterjohannes\DebugMcp\Tools;

use DateTimeZone;
use Mcp\Capability\Attribute\McpTool;

class ClockTool
{
    #[McpTool(
        name: 'clock',
        description: 'Get current time with customizable format and timezone'
    )]
    public function execute(
        string $format = 'Y-m-d H:i:s',
        string $timezone = 'UTC'
    ): array {
        // Validate timezone
        $validTimezones = DateTimeZone::listIdentifiers();
        if (! in_array($timezone, $validTimezones, true)) {
            return [
                'error' => 'Invalid timezone',
                'details' => sprintf(
                    'Timezone "%s" is not valid. Use one of the valid timezone identifiers (e.g., UTC, America/New_York, Europe/London)',
                    $timezone
                ),
            ];
        }

        try {
            $dateTime = new \DateTime('now', new DateTimeZone($timezone));
            $formattedTime = $dateTime->format($format);

            return [
                'time' => $formattedTime,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to generate time',
                'details' => $e->getMessage(),
            ];
        }
    }
}
