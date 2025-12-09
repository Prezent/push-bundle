<?php

namespace Prezent\PushBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Prezent\PushBundle\DependencyInjection\PrezentPushExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PrezentPushExtensionTest extends TestCase
{
    /**
     * @var PrezentPushExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->extension = new PrezentPushExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        $this->extension = null;
        $this->container = null;
    }

    public function testOneSignalConfigValuesAreSetCorrectly()
    {
        $applicationId = 'XXX-XXX';
        $applicationAuthKey = 'YYY-YYYY';
        $clientClass = 'OneSignal\OneSignal';

        $this->extension->load(
            [
                [
                    'provider' => 'onesignal',
                    'onesignal' => [
                        'application_id' => $applicationId,
                        'application_auth_key' => $applicationAuthKey,
                    ],
                ]
            ],
            $this->container
        );

        $this->assertEquals($applicationId, $this->container->getParameter('prezent_push.onesignal.application_id'));
        $this->assertEquals($applicationAuthKey, $this->container->getParameter('prezent_push.onesignal.application_auth_key'));

        // Verify that the container defines the correct OneSignal client class
        $definition = $this->container->getDefinition('OneSignal\\OneSignal');
        $this->assertEquals($clientClass, $definition->getClass());
    }

    public function testPushwooshConfigValuesAreSetCorrectly()
    {
        $applicationId = 'XXX-XXX';
        $applicationGroupId = 'YYY-YYYY';
        $apiKey = 'xxxxxxxxxxxxxx';
        $clientClass = 'Gomoob\Pushwoosh\Client\Pushwoosh';

        $this->extension->load(
            [
                [
                    'provider' => 'pushwoosh',
                    'pushwoosh' => [
                        'application_id' => $applicationId,
                        'application_group_id' => $applicationGroupId,
                        'api_key' => $apiKey,
                    ],
                ]
            ],
            $this->container
        );

        $this->assertEquals($applicationId, $this->container->getParameter('prezent_push.pushwoosh.application_id'));
        $this->assertEquals($applicationGroupId, $this->container->getParameter('prezent_push.pushwoosh.application_group_id'));
        $this->assertEquals($apiKey, $this->container->getParameter('prezent_push.pushwoosh.api_key'));

        // Verify that the container defines the correct Pushwoosh client class
        $definition = $this->container->getDefinition('Gomoob\\Pushwoosh\\Client\\Pushwoosh');
        $this->assertEquals($clientClass, $definition->getClass());
    }
}
