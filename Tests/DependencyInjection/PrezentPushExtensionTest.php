<?php

namespace Prezent\PushBundle\Tests\DependencyInjection;

use Prezent\PushBundle\DependencyInjection\PrezentPushExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PrezentPushExtensionTest extends \PHPUnit_Framework_TestCase
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
    protected function setUp()
    {
        $this->extension = new PrezentPushExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
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

        $client = $this->container->get('onesignal');
        $this->assertEquals($clientClass, get_class($client));
    }

    public function testPushwooshConfigValuesAreSetCorrectly()
    {
        $applicationId = 'XXX-XXX';
        $applicationGroupId = 'YYY-YYYY';
        $apiKey = 'xxxxxxxxxxxxxx';
        $clientClass = 'Gomoob\Pushwoosh\Client\PushwooshMock';

        $this->extension->load(
            [
                [
                    'provider' => 'pushwoosh',
                    'pushwoosh' => [
                        'application_id' => $applicationId,
                        'application_group_id' => $applicationGroupId,
                        'api_key' => $apiKey,
                        'client_class' => 'Gomoob\Pushwoosh\Client\PushwooshMock',
                    ],
                ]
            ],
            $this->container
        );

        $this->assertEquals($applicationId, $this->container->getParameter('prezent_push.pushwoosh.application_id'));
        $this->assertEquals($applicationGroupId, $this->container->getParameter('prezent_push.pushwoosh.application_group_id'));
        $this->assertEquals($apiKey, $this->container->getParameter('prezent_push.pushwoosh.api_key'));

        $client = $this->container->get('pushwoosh');
        $this->assertEquals($clientClass, get_class($client));
    }
}