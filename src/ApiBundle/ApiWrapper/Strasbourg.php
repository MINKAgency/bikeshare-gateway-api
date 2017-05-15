<?php
/**
 * Created by PhpStorm.
 * User: louisparrouy
 * Date: 15/05/2017
 * Time: 17:05
 */

namespace ApiBundle\ApiWrapper;

use ApiBundle\Model\BikeStation;

/**
 * @author Louis Parrouy <louis.parrouy@mink-agency.com>
 */
class Strasbourg extends AbstractCityWrapper
{
    const CITY_NAME       = 'Strasbourg';
    const COMMERCIAL_NAME = 'Vélhop';
    const COUNTRY_CODE    = 'FR';

    /**
     * Get all stations of the city.
     *
     * @return mixed
     */
    protected function getStations()
    {
        $ch = curl_init('http://velhop.strasbourg.eu/tvcstations.xml');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $arrayResponse = $this->arrayConverter->xmlToArray(simplexml_load_string($data));

        return $arrayResponse['vcs']['sl'];
    }

    public function getNearestStation($latitude, $longitude)
    {
        $stations              = $this->getStations();
        $nearestStation        = new BikeStation(static::COMMERCIAL_NAME);
        $nearestDistance       = -1;
        $nearestPos            = [];
        $currentNearestStation = null;

        foreach ($stations['si'] as $station) {
            $stationLat      = $station['@la'];
            $stationLon      = $station['@lg'];
            $currentDistance = $this
                ->distanceCalculator
                ->distance($latitude, $longitude, $stationLat, $stationLon, 'K')
            ;

            if ($currentDistance < $nearestDistance || $nearestDistance === -1) {
                $nearestDistance       = $currentDistance;
                $nearestPos['lat']     = floatval($stationLat);
                $nearestPos['long']    = floatval($stationLon);
                $currentNearestStation = $station;
            }
        }

        $nearestStation->setName($currentNearestStation['@na']);
        $nearestStation->setAvailableBikes(intval($currentNearestStation['@av']));
        $nearestStation->setAvailableBikeStands(intval($currentNearestStation['@fr']));
        $nearestStation->setDistance($nearestDistance);
        $nearestStation->setPosition($nearestPos);
        $nearestStation->setNumber(intval($currentNearestStation['@to']));

        return $nearestStation;
    }
}