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
     * @return array
     */
    protected function formatInputArray(array $data)
    {
        $customData = [];
        foreach ($data as $dataItem) {
            // check for the integrity of the data
            if (!strstr($dataItem, ':')) {
                throw new \InvalidArgumentException('Custom data items must be entered in the format `key`:`value`');
            }

            // and parse the data
            list($key, $value) = explode(':', $dataItem);
            $customData[$key] = $value;
        }

        return $customData;
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
