<?php

declare(strict_types=1);

namespace Prezent\PushBundle\Manager;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
interface ManagerInterface
{
    /**
     * Send a push notification
     *
     * @param array<string, string> $contents [
     *      'language-code' => 'content'
     *  ]
     * @param array<string, mixed> $data
     * @param array<string> $devices
     * @param array<string, mixed> $parameters
     */
    public function send(array $contents, array $data = [], array $devices = [], array $parameters = []): bool;

    /**
     * Send a push notification, with a specific title
     *
     * @param array<string, string> $titles [
     *      'language-code' => 'title'
     *  ]
     * @param array<string, string> $contents [
     *      'language-code' => 'content'
     *  ]
     * @param array<string, mixed> $data
     * @param array<string> $devices
     * @param array<string, mixed> $parameters
     */
    public function sendWithTitle(
        array $titles,
        array $contents,
        array $data = [],
        array $devices = [],
        array $parameters = []
    ): bool;

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
    public function sendBatch(array $notifications): bool;

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
    public function getErrorCode(): ?int;

    /**
     * @return string
     */
    public function getErrorMessage(): ?string;
}
