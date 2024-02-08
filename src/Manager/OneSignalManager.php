<?php

declare(strict_types=1);

namespace Prezent\PushBundle\Manager;

use OneSignal\OneSignal;
use Prezent\PushBundle\Exception\LoggingException;
use Prezent\PushBundle\Log\LoggingTrait;
use Psr\Log\LoggerInterface;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class OneSignalManager implements ManagerInterface
{
    use LoggingTrait;

    private OneSignal $client;

    private ?string $errorMessage = null;

    private ?int $errorCode = null;

    private ?LoggerInterface $logger = null;

    private bool $logging;

    public function __construct(OneSignal $client, bool $logging = false)
    {
        $this->client = $client;
        $this->logging = $logging;
    }

    public function send(array $contents, array $data = [], array $devices = [], array $parameters = []): bool
    {
        $notificationData = [
            'contents' => [
                'en' => $contents,
            ],
            // make sure the devices array has numeric keys, otherwise it serializes in a wrong way (object i/o array)
            'include_aliases' => [
                'onesignal_id' => array_values($devices),
            ],
            'target_channel' => 'push',
        ];

        if (!empty($data)) {
            $notificationData['data'] = $data;
        }

        $notificationData = array_merge($notificationData, $parameters);

        return $this->sendPush($notificationData);
    }

    /**
     * {@inheritdoc}
     */
    public function sendBatch(array $notifications): bool
    {
        $success = true;

        foreach ($notifications as $notification) {
            $result = call_user_func_array(array($this, "send"), $notification);

            if (!$result) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function directSend(array $notificationData)
    {
        return $this->sendPush($notificationData);
    }

    /**
     * Send the push message with client
     *
     * @param array $notificationData
     * @return bool
     */
    private function sendPush(array $notificationData)
    {
        // Call the REST Web Service
        $response = $this->client->notifications()->add($notificationData);

        // Check if its ok
        if (!isset($response['errors'])) {
            if ($this->logging) {
                $this->log($notificationData, true);
            }

            return true;
        } else {
            if ($this->logging) {
                $this->log(
                    $notificationData,
                    false,
                    ['message' => implode('; ', $response['errors']), 'code' => null]
                );
            }

            $this->errorMessage = implode('; ', $response['errors']);
            $this->errorCode = null;

            return false;
        }
    }

    /**
     * Log the notification
     *
     * @param array $notificationData
     * @param bool $success
     * @param array $context
     * @return bool
     * @throws LoggingException
     */
    protected function log(array $notificationData, bool $success = true, array $context = []): bool
    {
        switch ($this->logging) {
            case 'file':
                if (null === $this->logger) {
                    throw new LoggingException('No logger is set, cannot write to file');
                }

                $this->logToFile($this->logger, $notificationData, $success, $context);
                break;
            default:
                break;
        }

        return true;
    }

    /**
     * Getter for errorMessage
     *
     * @return string
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Getter for errorCode
     *
     * @return int
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Getter for logger
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Setter for logger
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
