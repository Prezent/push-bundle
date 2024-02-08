<?php

declare(strict_types=1);

namespace Prezent\PushBundle\Traits;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
trait DataTrait
{
    /**
     * Format the custom data into an associative array
     *
     * @param array<string> $data
     * @return array<string, mixed>
     */
    protected function formatInputArray(array $data, bool $strongTyping = false): array
    {
        $customData = [];
        foreach ($data as $dataItem) {
            // check for the integrity of the data
            if (!strstr($dataItem, ':')) {
                throw new \InvalidArgumentException('Custom data items must be entered in the format `key`:`value`');
            }

            // and parse the data
            list($key, $value) = explode(':', $dataItem);
            $customData[$key] = $strongTyping ? $this->formatValue($value) : $value;
        }

        return $customData;
    }

    /**
     * Convert strings to numeric or their other type
     *
     * @param mixed $value
     */
    private function formatValue($value): float
    {
        if (!is_numeric($value)) {
            // hacky? way to detect a float
            return (float) $value;
        }

        return $value;
    }
}
