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

    public function testConfigValuesAreSetCorrectly()
    {
        $applicationId = 'XXX-XXX';
        $applicationGroupId = 'YYY-YYYY';
        $apiKey = 'xxxxxxxxxxxxxx';
        $clientClass = 'Gomoob\Pushwoosh\Client\PushwooshMock';
        $logRequests = 'log';

        $this->extension->load(
            [
                [
                    'application_id' => $applicationId,
                    'application_group_id' => $applicationGroupId,
                    'api_key' => $apiKey,
                    'client_class' => 'Gomoob\Pushwoosh\Client\PushwooshMock',
                    'log_requests' => 'log',
                ]
            ],
            $this->container
        );

        $this->assertEquals($applicationId, $this->container->getParameter('prezent_pushwoosh.application_id'));
        $this->assertEquals($applicationGroupId, $this->container->getParameter('prezent_pushwoosh.application_group_id'));
        $this->assertEquals($apiKey, $this->container->getParameter('prezent_pushwoosh.api_key'));
        $this->assertEquals($logRequests, $this->container->getParameter('prezent_pushwoosh.log_requests'));

        $client = $this->container->get('pushwoosh');
        $this->assertEquals($clientClass, get_class($client));
    }
}