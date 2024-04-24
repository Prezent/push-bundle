<?php

declare(strict_types=1);

namespace Prezent\PushBundle\Manager;

use Gomoob\Pushwoosh\IPushwoosh;
use Gomoob\Pushwoosh\Model\Notification\Notification;
use Gomoob\Pushwoosh\Model\Request\CreateMessageRequest;
use Prezent\PushBundle\Exception\LoggingException;
use Prezent\PushBundle\Log\LoggingTrait;
use Psr\Log\LoggerInterface;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class PushwooshManager implements ManagerInterface
{
    use LoggingTrait;

    private IPushwoosh $client;

    private ?string $errorMessage = null;

    private ?int $errorCode = null;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private bool $logging;

    public function __construct(IPushwoosh $client, bool $logging = false)
    {
        $this->client = $client;
        $this->logging = $logging;
    }

    /**
     * {@inheritdoc}
     */
    public function send(array $contents, array $data = [], array $devices = [], array $parameters = []): bool
    {
        $notification = $this->createNotification($contents, [], $data, $devices);

        $request = new CreateMessageRequest();
        $request->addNotification($notification);

        return $this->sendPush($request);
    }

    /**
     * {@inheritdoc}
     */
    public function sendWithTitle(
        array $titles,
        array $contents,
        array $data = [],
        array $devices = [],
        array $parameters = []
    ): bool {
        $notification = $this->createNotification($contents, $titles, $data, $devices);

        $request = new CreateMessageRequest();
        $request->addNotification($notification);

        return $this->sendPush($request);
    }

    /**
     * {@inheritdoc}
     */
    public function sendBatch(array $notifications): bool
    {
        $request = new CreateMessageRequest();
        foreach ($notifications as $notificationArray) {
            $content = $notificationArray['content'] ?? '';
            $data = $notificationArray['data'] ?? [];
            $devices = $notificationArray['devices'] ?? [];

            if ($content) {
                $notification = $this->createNotification($content, $data, $devices);
                $request->addNotification($notification);
            }
        }

        return $this->sendPush($request);
    }

    /**
     * {@inheritdoc}
     */
    public function directSend(array $notificationData)
    {
        $notification = $this->createNotification(...$notificationData);

        $request = new CreateMessageRequest();
        $request->addNotification($notification);

        return $this->sendPush($request);
    }

    /**
     * Create a notification for the request
     *
     * @param array<string, string> $content
     * @param array $data
     * @param array $devices
     */
    private function createNotification(
        array $content,
        array $title = [],
        array $data = [],
        array $devices = []
    ): Notification {
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setContent($content);

        if (!empty($data)) {
            $notification->setData($data);
        }

        if (!empty($devices)) {
            $notification->setDevices($devices);
        }

        return $notification;
    }

    /**
     * Send the push message with client
     *
     * @param CreateMessageRequest $request
     * @return boolean
     */
    private function sendPush(CreateMessageRequest $request): bool
    {
        // Call the REST Web Service
        $response = $this->client->createMessage($request);

        // Check if its ok
        if ($response->isOk()) {
            if ($this->logging) {
                // log all individual messages
                foreach ($request->getNotifications() as $notification) {
                    $this->log($notification, true);
                }
            }

            return true;
        } else {
            if ($this->logging) {
                foreach ($request->getNotifications() as $notification) {
                    $this->log(
                        $notification,
                        false,
                        ['message' => $response->getStatusMessage(), 'code' => $response->getStatusCode()]
                    );
                }
            }

            $this->errorMessage = $response->getStatusMessage();
            $this->errorCode = $response->getStatusCode();

            return false;
        }
    }

    /**
     * Log the notification
     *
     * @param Notification $notification
     * @param bool $success
     * @param array $context
     * @return bool
     * @throws LoggingException
     */
    protected function log(Notification $notification, bool $success = true, array $context = [])
    {
        switch ($this->logging) {
            case 'file':
                if (null === $this->logger) {
                    throw new LoggingException('No logger is set, cannot write to file');
                }

                $data = [
                    'receivers' => $notification->getDevices(),
                    'content' => $notification->getContent(),
                    'data' => $notification->getData(),
                ];

                $this->logToFile($this->logger, $data, $success, $context);
                break;
            default:
                break;
        }

        return true;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
