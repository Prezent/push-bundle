<?php

declare(strict_types=1);

namespace Prezent\PushBundle\Command;

use Prezent\PushBundle\Manager\ManagerInterface;
use Prezent\PushBundle\Traits\DataTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class SendPushCommand extends Command
{
    use DataTrait;

    private ManagerInterface $pushManager;

    public function __construct(ManagerInterface $pushManager)
    {
        $this->pushManager = $pushManager;
        parent::__construct();
    }

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
            ->addOption(
                'parameters',
                'p',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'List of parameters to include in the notification. Format `key`:`value`',
                []
            )
            ->setHelp('Send a push message manually');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $success = $this->pushManager->send(
            $input->getArgument('message'),
            $this->formatInputArray($input->getOption('custom-data')),
            $input->getOption('tokens'),
            $this->formatInputArray($input->getOption('parameters'), true)
        );

        // Check if it's ok
        if ($success) {
            $output->writeln('<info>Push message send successful</info>');
        } else {
            $output->writeln('<error>Push message could not be send</error>');
            $output->writeln(
                sprintf(
                    '<error>[%d] %s</error>',
                    $this->pushManager->getErrorCode(),
                    $this->pushManager->getErrorMessage()
                )
            );
        }

        return Command::SUCCESS;
    }
}
