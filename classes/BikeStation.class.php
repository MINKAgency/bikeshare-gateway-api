<?php

/**
 * Created by PhpStorm.
 * User: Guillaume Isselin
 * Date: 08/02/2016
 * Time: 17:07
 */
class BikeStation
{
    /**
     * BikeStation constructor.
     */
    public function __construct($bikes_name = null)
    {
        $this->commercial_name = $bikes_name;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getAvailableBikeStands()
    {
        return $this->available_bike_stands;
    }

    /**
     * @param mixed $available_bike_stands
     */
    public function setAvailableBikeStands($available_bike_stands)
    {
        $this->available_bike_stands = $available_bike_stands;
    }

    /**
     * @return mixed
     */
    public function getAvailableBikes()
    {
        return $this->available_bikes;
    }

    /**
     * @param mixed $available_bikes
     */
    public function setAvailableBikes($available_bikes)
    {
        $this->available_bikes = $available_bikes;
    }

    /**
     * @return mixed
     */
    public function getLastUpdate()
    {
        return $this->last_update;
    }

    /**
     * @param mixed $last_update
     */
    public function setLastUpdate($last_update)
    {
        $this->last_update = $last_update;
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
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return mixed
     */
    public function getCommercialName()
    {
        return $this->commercial_name;
    }

    /**
     * @param mixed $commercial_name
     */
    public function setCommercialName($commercial_name)
    {
        $this->commercial_name = $commercial_name;
    }
    public $number;
    public $name;
    public $position;
    public $available_bike_stands;
    public $available_bikes;
    public $last_update;
    public $distance;
    public $commercial_name;



}