<?php

namespace ApiBundle\ApiWrapper;

use ApiBundle\Model\BikeStation;
use ApiBundle\Service\ArrayConverter;
use ApiBundle\Service\DistanceCalculator;
use GuzzleHttp\Client;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 */
abstract class AbstractCityWrapper
{
    const CITY_NAME       = '';
    const COMMERCIAL_NAME = '';
    const COUNTRY_CODE    = '';

    /**
     * The apiKey.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The arrayConverter.
     *
     * @var ArrayConverter
     */
    protected $arrayConverter;

    /**
     * The distanceCalculator.
     *
     * @var DistanceCalculator
     */
    protected $distanceCalculator;

    /**
     * The guzzleClient.
     *
     * @var Client
     */
    protected $guzzleClient;

    /**
     * AbstractCityWrapper constructor.
     *
     * @param Client             $guzzleClient
     * @param ArrayConverter     $arrayConverter
     * @param DistanceCalculator $distanceCalculator
     * @param string             $apiKey
     */
    public function __construct(
        Client $guzzleClient,
        ArrayConverter $arrayConverter,
        DistanceCalculator $distanceCalculator,
        $apiKey = ''
    ) {
        $this->guzzleClient       = $guzzleClient;
        $this->apiKey             = $apiKey;
        $this->arrayConverter     = $arrayConverter;
        $this->distanceCalculator = $distanceCalculator;
    }

    /**
     * Get the same format as JCDecauxAPI.
     *
     * @return array
     */
    public function getFormattedCity()
    {
        return [
            'name'            => static::CITY_NAME,
            'commercial_name' => static::COMMERCIAL_NAME,
            'country_code'    => static::COUNTRY_CODE,
        ];
    }

    /**
     * Return the nearest station for the current city.
     *
     * @param int $latitude
     * @param int $longitude
     *
     * @return BikeStation
     */
    abstract public function getNearestStation($latitude, $longitude);
}
