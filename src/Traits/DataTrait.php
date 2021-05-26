<?php

namespace Prezent\PushBundle\Traits;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
trait DataTrait
{
    /**
     * Format the custom data into an associative array
     *
     * @param array $data
     * @param bool $strongTyping
     * @return array
     */
    protected function formatInputArray(array $data, $strongTyping = false)
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
     * @param $value
     * @return float|int
     */
    private function formatValue($value)
    {
        if (is_numeric($value)) {
            // hacky? way to detect a float
            return (strpos($value, ".") !== false) ? (float) $value : (int) $value;
        }

        return $value;
    }

    /**
     * Stub function for legacy purposes
     *
     * @deprecated use `formatInputArray` instead
     * @param array $data
     * @return array
     */
    protected function formatCustomData(array $data)
    {
        return $this->formatInputArray($data);
    }
}
