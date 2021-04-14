<?php

namespace Prezent\PushBundle\Command;

use Prezent\PushBundle\Manager\ManagerInterface;
use Prezent\PushBundle\Traits\DataTrait;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class SendPushCommand extends ContainerAwareCommand
{
    use DataTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('push:send')
            ->setDescription('Send a push message manually')
            ->addArgument('message', InputArgument::REQUIRED, 'The message to send')
            ->addOption(
                'custom-data',
                'd',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'List of custom data items to be send with the request. Format `key`:`value`',
                []
            )
            ->addOption(
                'tokens',
                't',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'List of push token to send the notification to',
                []
            )
            ->setHelp('Send a push message manually')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ManagerInterface $pushManager */
        $pushManager = $this->getContainer()->get('prezent_push.manager');

        $customData = [];
        if ($data = $input->getOption('custom-data')) {
            $customData = $this->formatCustomData($data);
        }

        $success = $pushManager->send($input->getArgument('message'), $customData, $input->getOption('tokens'));

        // Check if its ok
        if ($success) {
            $output->writeln('<info>Push message send successful</info>');
        } else {
            $output->writeln('<error>Push message could not be send</error>');
            $output->writeln(
                sprintf(
                    '<error>[%d] %s</error>',
                    $pushManager->getErrorCode(),
                    $pushManager->getErrorMessage()
                )
            );
        }

        return 0;
    }
}
