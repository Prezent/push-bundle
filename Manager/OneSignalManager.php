<?php

namespace Prezent\PushBundle\Manager;

use OneSignal\OneSignal;
use Prezent\PushBundle\Exception\LoggingException;
use Prezent\PushBundle\Log\LoggingTrait;
use Psr\Log\LoggerInterface;

/**
 * Prezent\PushBundle\PushNotification\Manager
 *
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class OneSignalManager implements ManagerInterface
{
    use LoggingTrait;

    /**
     * @var OneSignal
     */
    private $client;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $logging = null;

    /**
     * Constructor
     *
     * @param OneSignal $client
     * @param string $logging
     */
    public function __construct(OneSignal $client, $logging = null)
    {
        $this->client = $client;
        $this->logging = $logging;
    }

    /**
     * {@inheritdoc}
     */
    public function send($content, array $data = [], array $devices = [])
    {
        $notificationData = [
            'contents' => [
                'en' => $content,
            ],
            'include_player_ids' => $devices,
            'data' => $data,
        ];
        
        return $this->sendPush($notificationData);
    }

    /**
     * {@inheritdoc}
     */
    public function sendBatch(array $notifications)
    {
        foreach ($notifications as $notification) {
            call_user_func_array(array($this, "send"), $notification);
        }

        return true;
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
        $response = $this->client->notifications->add($notificationData);

        // Check if its ok
        if (!isset($response['error'])) {
            if ($this->logging) {
                $this->log($notificationData, true);
            }
            return true;
        } else {
            if ($this->logging) {
                $this->log(
                    $notificationData,
                    false,
                    ['message' => implode('; ', $response['error']), 'code' => null]
                );
            }

            $this->errorMessage = implode('; ', $response['error']);
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
    protected function log(array $notificationData, $success = true, array $context = [])
    {
        switch ($this->logging) {
            case 'file':
                if (null === $this->logger) {
                    throw new LoggingException('No logger is set, cannot write to file');
                }

                $data = [
                    'receivers' => $notificationData['include_player_ids'],
                    'content' => $notificationData['contents'],
                    'data' => $notificationData['data'],
                ];

                $this->logToFile($this->logger, $data, $success, $context);
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
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Getter for errorCode
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Getter for logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Setter for logger
     *
     * @param LoggerInterface $logger
     * @return self
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
