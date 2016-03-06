<?php

namespace ApiBundle\ApiWrapper;

use ApiBundle\Model\BikeStation;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 */
class Nice extends AbstractCityWrapper
{
    const CITY_NAME       = 'Nice';
    const COMMERCIAL_NAME = 'Vélo Bleu';
    const COUNTRY_CODE    = 'FR';

    /**
     * {@inheritdoc}
     */
    public function getNearestStation($latitude, $longitude)
    {
        $stations = $this->getStations();

        $nearestStation        = new BikeStation(static::COMMERCIAL_NAME);
        $nearestDistance       = -1;
        $nearestPos            = [];
        $currentNearestStation = null;

        foreach ($stations['stations'] as $station) {
            $consideredElement = $station;
            if (intval($consideredElement['disp']) !== 1 || !(intval($consideredElement['ab']) > 0)) {
                continue;
            }

            $currentDistance = $this
                ->distanceCalculator
                ->distance(
                    $latitude,
                    $longitude,
                    floatval($consideredElement['lat']),
                    floatval($consideredElement['lng']),
                    'K'
                )
            ;

            if ($currentDistance < $nearestDistance || $nearestDistance === -1) {
                $nearestDistance       = $currentDistance;
                $nearestPos['lat']     = floatval($consideredElement['lat']);
                $nearestPos['long']    = floatval($consideredElement['lng']);
                $currentNearestStation = $consideredElement;
            }
        }

        $nearestStation->setName(
            urldecode(
                empty($currentNearestStation['wcom']) ? $currentNearestStation['name'] : $currentNearestStation['wcom']
            )
        );
        $nearestStation->setAvailableBikes(intval($currentNearestStation['ab']));
        $nearestStation->setAvailableBikeStands(intval($currentNearestStation['ab']));
        $nearestStation->setDistance($nearestDistance);
        $nearestStation->setPosition($nearestPos);
        $nearestStation->setNumber(intval($currentNearestStation['id']));
        $epoch = strtotime($stations['time']);
        $nearestStation->setLastUpdate(new \DateTime("@$epoch"));

        return $nearestStation;
    }

    /**
     * Get all stations of the city.
     *
     * @return mixed
     */
    protected function getStations()
    {
        $ch = curl_init('https://www.velobleu.org/cartoV2/libProxyCarto.asp');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($data);

        $response['stations'] = $this->arrayConverter->stdArrayToArray($data->stand);
        $response['time']     = $data->gmt;

        return $response;
    }
}
