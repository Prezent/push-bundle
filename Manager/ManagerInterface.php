<?php

namespace Prezent\PushBundle\Manager;

/**
 * Prezent\PushBundle\Manager\ManagerInterface
 *
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
interface ManagerInterface
{
    /**
     * Send a push notification
     *
     * @param string $content
     * @param array $data
     * @param array $devices
     * @return bool
     */
    public function send($content, array $data = [], array $devices = []);

    /**
     * Send a batch of push notifications in one go
     *
     * @param array $notifications [
     *      'content' => string,
     *      'data' => [],
     *      'devices' => [],
     * ]
     * @return bool
     */
    public function sendBatch(array $notifications);

    /**
     * @return int
     */
    public function getErrorCode();

    /**
     * @return string
     */
    public function getErrorMessage();
}