<?php

namespace ApiBundle\ApiWrapper;

use ApiBundle\Model\BikeStation;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 */
class Bordeaux extends AbstractCityWrapper
{
    const CITY_NAME       = 'Bordeaux';
    const COMMERCIAL_NAME = 'VCub';
    const COUNTRY_CODE    = 'FR';

    /**
     * Get all stations of the city.
     *
     * @return mixed
     */
    protected function getStations()
    {
        $ch = curl_init(
            'http://data.bordeaux-metropole.fr/wfs?key='
            .$this->apiKey.
            '&SERVICE=WFS&VERSION=1.1.0&REQUEST=GetFeature&TYPENAME=CI_VCUB_P&SRSNAME=EPSG:4326'
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $arrayResponse = $this->arrayConverter->xmlToArray(simplexml_load_string($data));

        return $arrayResponse['FeatureCollection']['gml:featureMember'];
    }

    /**
     * {@inheritdoc}
     */
    public function getNearestStation($latitude, $longitude)
    {
        $stations              = $this->getStations();
        $nearestStation        = new BikeStation(static::COMMERCIAL_NAME);
        $nearestDistance       = -1;
        $nearestPos            = [];
        $currentNearestStation = null;

        foreach ($stations as $station) {
            $consideredElement = $station['bm:CI_VCUB_P'];
            if ($consideredElement['bm:ETAT'] !== 'CONNECTEE' || !(intval($consideredElement['bm:NBVELOS']) > 0)) {
                continue;
            }

            $latLong         = explode(' ', $consideredElement['bm:geometry']['gml:Point']['gml:pos']);
            $stationLat      = floatval($latLong[0]);
            $stationLon      = floatval($latLong[1]);
            $currentDistance = $this
                ->distanceCalculator
                ->distance($latitude, $longitude, $stationLat, $stationLon, 'K')
            ;

            if ($currentDistance < $nearestDistance || $nearestDistance === -1) {
                $nearestDistance       = $currentDistance;
                $nearestPos['lat']     = floatval($stationLat);
                $nearestPos['long']    = floatval($stationLon);
                $currentNearestStation = $consideredElement;
            }
        }

        $nearestStation->setName($currentNearestStation['bm:NOM']);
        $nearestStation->setAvailableBikes(intval($currentNearestStation['bm:NBVELOS']));
        $nearestStation->setAvailableBikeStands(intval($currentNearestStation['bm:NBPLACES']));
        $nearestStation->setDistance($nearestDistance);
        $nearestStation->setPosition($nearestPos);
        $nearestStation->setNumber(intval($currentNearestStation['bm:GID']));
        $nearestStation->setLastUpdate(strtotime($currentNearestStation['bm:HEURE']));

        return $nearestStation;
    }
}
