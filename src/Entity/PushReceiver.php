<?php

namespace Prezent\PushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>

 * @ORM\MappedSuperclass
 */
class PushReceiver
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="push_token", type="string")
     */
    protected $pushToken;

    /**
     * @var int
     * @ORM\Column(name="device_type", type="integer")
     */
    protected $deviceType;

    /**
     * @var string
     * @ORM\Column(name="identifier", type="string")
     */
    protected $identifier;

    /**
     * @var string
     * @ORM\Column(name="language", type="string", length=2)
     */
    protected $language;

    /**
     * @var int
     * @ORM\Column(name="timezone", type="integer", nullable=true)
     */
    protected $timezone;

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Getter for pushToken
     *
     * @return string
     */
    public function getPushToken()
    {
        return $this->pushToken;
    }

    /**
     * Setter for pushToken
     *
     * @param string $pushToken
     * @return self
     */
    public function setPushToken($pushToken)
    {
        $this->pushToken = $pushToken;
        return $this;
    }

    /**
     * Getter for deviceType
     *
     * @return int
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }

    /**
     * Setter for deviceType
     *
     * @param int $deviceType
     * @return self
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;
        return $this;
    }

    /**
     * Getter for identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter for identifier
     *
     * @param string $identifier
     * @return self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Getter for language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Setter for language
     *
     * @param string $language
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Getter for timezone
     *
     * @return int
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Setter for timezone
     *
     * @param int $timezone
     * @return self
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }
}
