<?php

namespace Prezent\PushwooshBundle\Log;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Gomoob\Pushwoosh\Model\Notification\Notification;
use Prezent\PushwooshBundle\Entity\LogEntry;
use Psr\Log\LoggerInterface;

/**
 * Trait containing functions to log push notificationsz
 *
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
trait LoggingTrait
{
    /**
     * @param LoggerInterface $logger
     * @param array $data
     * @param bool $success
     * @param array $context
     * @return bool
     */
    public function logToFile(LoggerInterface $logger, array $data, $success = true, array $context = [])
    {
        $data = array_merge($data, $context);

        if ($success) {
            $logger->info('Pushmessage sent', $data);
        } else {
            $logger->error('Error sending pushmessage', $data);

        }

        return true;
    }
}
