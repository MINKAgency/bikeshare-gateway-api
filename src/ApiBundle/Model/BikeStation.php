<?php

namespace ApiBundle\Model;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 * @author Guillaume Isselin
 */
class BikeStation
{
    /**
     * @var int
     */
    protected $number;

    /**
     * @var string
     */
    protected $name;

    protected $position;

    protected $availableBikeStands;
    protected $availableBikes;
    protected $lastUpdate;
    protected $distance;
    protected $commercialName;

    /**
     * BikeStation constructor.
     */
    public function __construct($bikes_name = null)
    {
        $this->commercialName = $bikes_name;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return BikeStation
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return BikeStation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     *
     * @return BikeStation
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvailableBikeStands()
    {
        return $this->availableBikeStands;
    }

    /**
     * @param mixed $availableBikeStands
     *
     * @return BikeStation
     */
    public function setAvailableBikeStands($availableBikeStands)
    {
        $this->availableBikeStands = $availableBikeStands;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvailableBikes()
    {
        return $this->availableBikes;
    }

    /**
     * @param mixed $availableBikes
     *
     * @return BikeStation
     */
    public function setAvailableBikes($availableBikes)
    {
        $this->availableBikes = $availableBikes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param mixed $lastUpdate
     *
     * @return BikeStation
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param mixed $distance
     *
     * @return BikeStation
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     */
    public function getCommercialName()
    {
        return $this->commercialName;
    }

    /**
     * @param null $commercialName
     *
     * @return BikeStation
     */
    public function setCommercialName($commercialName)
    {
        $this->commercialName = $commercialName;

        return $this;
    }
}
