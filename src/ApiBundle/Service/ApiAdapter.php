<?php

namespace ApiBundle\Service;

use ApiBundle\Model\BikeStation;
use ApiBundle\Registry\ApiRegistry;
use DateTime;
use JCDodatawrapper\Vls\Wrapper as JCWrapper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 */
class ApiAdapter
{
    /**
     * The apiWrapperRegistry.
     *
     * @var ApiRegistry
     */
    protected $apiWrapperRegistry;

    /**
     * The jcDecauxWrapper.
     *
     * @var JCWrapper
     */
    protected $jcDecauxWrapper;

    /**
     * The availableCities.
     *
     * @var array
     */
    protected $availableCities = [];

    /**
     * The distanceCalculator.
     *
     * @var DistanceCalculator
     */
    protected $distanceCalculator;

    /**
     * The arrayConverter.
     *
     * @var ArrayConverter
     */
    protected $arrayConverter;

    /**
     * ApiAdapter constructor.
     *
     * @param ApiRegistry        $apiRegistry
     * @param JCWrapper          $wrapper
     * @param DistanceCalculator $calculator
     * @param ArrayConverter     $arrayConverter
     */
    public function __construct(
        ApiRegistry $apiRegistry,
        JCWrapper $wrapper,
        DistanceCalculator $calculator,
        ArrayConverter $arrayConverter
    ) {
        $this->apiWrapperRegistry = $apiRegistry;
        $this->jcDecauxWrapper    = $wrapper;
        $this->distanceCalculator = $calculator;
        $this->arrayConverter     = $arrayConverter;
        $this->initCityAvailable();
    }

    /**
     * Init the array of our available cities.
     */
    protected function initCityAvailable()
    {
        $citisFromAPI = $this->apiWrapperRegistry->getAllCityWrapped();

        $availableCities = array_map(function ($element) {
            return $element->getFormattedCity();
        }, $citisFromAPI);

        $availableCities = array_merge(
            $availableCities,
            $this->arrayConverter->stdArrayToArray($this->jcDecauxWrapper->getContracts())
        );

        $availableCompleteCities = [];

        foreach ($availableCities as $city) {
            $availableCompleteCities[$city['name']] =
                [
                    'commercial_name' => $city['commercial_name'],
                    'country_code'    => $city['country_code'],
                ];
        }

        $this->availableCities = $availableCompleteCities;
    }

    /**
     * Get all available cities by alphabetical order.
     *
     * @return array
     */
    public function getAvailableCities()
    {
        $cities = array_keys($this->availableCities);
        sort($cities);

        return $cities;
    }

    /**
     * Return the nearest BikeStation.
     *
     * @param string|null $latitude
     * @param string|null $longitude
     * @param string|null $city
     *
     * @return BikeStation
     */
    public function getNearestStation($latitude = null, $longitude = null, $city = null)
    {
        if (!in_array($city, $this->getAvailableCities())) {
            throw new NotFoundHttpException('The City does not exist');
        }
        if (!$this->isManagedByJCDecaux($city)) {
            $cityService = $this->apiWrapperRegistry->getCityWrapperByAlias($city);

            return $cityService->getNearestStation($latitude, $longitude);
        }

        return $this->getJcDecauxNearestStation(
            $latitude,
            $longitude,
            $this->arrayConverter->stdArrayToArray($this->jcDecauxWrapper->getStationsByContract($city))
        );
    }

    /**
     * Return if it's a city managed by JCDecaux.
     *
     * @param string $city
     *
     * @return bool
     */
    protected function isManagedByJCDecaux($city)
    {
        return !array_key_exists($city, $this->apiWrapperRegistry->getAllCityWrapped());
    }

    /**
     * Return the nearest BikeStation which is managed by JcDecaux API.
     *
     * @param string $latitude
     * @param string $longitude
     * @param array  $city
     *
     * @return BikeStation
     */
    protected function getJcDecauxNearestStation($latitude, $longitude, $city)
    {
        $nearestStation        = new BikeStation($this->availableCities[$city[0]['contract_name']]['commercial_name']);
        $nearestDistance       = -1;
        $nearestPos            = [];
        $currentNearestStation = null;

        foreach ($city as $bikePlace) {
            if ($bikePlace['status'] !== 'OPEN' || !($bikePlace['available_bikes'] > 0)) {
                continue;
            }

            $currentDistance = $this
                ->distanceCalculator
                ->distance($latitude, $longitude, $bikePlace['position']['lat'], $bikePlace['position']['lng'], 'K')
            ;

            if ($currentDistance < $nearestDistance || $nearestDistance === -1) {
                $nearestDistance       = $currentDistance;
                $nearestPos            = $bikePlace['position'];
                $currentNearestStation = $bikePlace;
            }
        }

        $nearestStation->setName($currentNearestStation['name']);
        $nearestStation->setAvailableBikes(intval($currentNearestStation['available_bikes']));
        $nearestStation->setAvailableBikeStands(intval($currentNearestStation['available_bike_stands']));
        $nearestStation->setDistance($nearestDistance);
        $nearestStation->setPosition($nearestPos);
        $nearestStation->setNumber(intval($currentNearestStation['number']));
        $epoch =  substr($currentNearestStation['last_update'], 0, -3);
        $nearestStation->setLastUpdate(new DateTime("@$epoch"));

        return $nearestStation;
    }
}
