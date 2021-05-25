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
     * @param array $parameters
     *
     * @return bool
     */
    public function send($content, array $data = [], array $devices = [], array $parameters = []);

    /**
     * Send a batch of push notifications in one go
     *
     * @param array $notifications [
     *      'content' => string,
     *      'data' => [],
     *      'devices' => [],
     *      'parameters' => [],
     * ]
     * @return bool
     */
    public function sendBatch(array $notifications);

    /**
     * Send a push notification with custom data
     *
     * @param array $notificationData
     * @return mixed
     */
    public function directSend(array $notificationData);

    /**
     * @return int
     */
    public function getErrorCode();

    /**
     * @return string
     */
    public function getErrorMessage();
}
