<?php

class BikeController
{
    private $supported_cities = array(BORDEAUX, NICE, NANTES, MARSEILLE);

    private $apis;

    /**
     * BikeController constructor.
     */
    public function __construct()
    {
       $this->apis = [
        BORDEAUX => 'http://data.bordeaux-metropole.fr/wfs?key='.BORDEAUX_API_KEY.'&SERVICE=WFS&VERSION=1.1.0&REQUEST=GetFeature&TYPENAME=CI_VCUB_P&SRSNAME=EPSG:4326',
        NICE => 'https://www.velobleu.org/cartoV2/libProxyCarto.asp',
        NANTES => 'https://api.jcdecaux.com/vls/v1/stations?contract=Nantes&apiKey=' . JCDECAUX_API_KEY,
        MARSEILLE => 'https://api.jcdecaux.com/vls/v1/stations?contract=Marseille&apiKey=' . JCDECAUX_API_KEY
    ];
    }

    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*::                                                                         :*/
    /*::  This routine calculates the distance between two points (given the     :*/
    /*::  latitude/longitude of those points). It is being used to calculate     :*/
    /*::  the distance between two locations using GeoDataSource(TM) Products    :*/
    /*::                                                                         :*/
    /*::  Definitions:                                                           :*/
    /*::    South latitudes are negative, east longitudes are positive           :*/
    /*::                                                                         :*/
    /*::  Passed to function:                                                    :*/
    /*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
    /*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
    /*::    unit = the unit you desire for results                               :*/
    /*::           where: 'M' is statute miles (default)                         :*/
    /*::                  'K' is kilometers                                      :*/
    /*::                  'N' is nautical miles                                  :*/
    /*::  Worldwide cities and other features databases with latitude longitude  :*/
    /*::  are available at http://www.geodatasource.com                          :*/
    /*::                                                                         :*/
    /*::  For enquiries, please contact sales@geodatasource.com                  :*/
    /*::                                                                         :*/
    /*::  Official Web site: http://www.geodatasource.com                        :*/
    /*::                                                                         :*/
    /*::         GeoDataSource.com (C) All Rights Reserved 2015		   		     :*/
    /*::                                                                         :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    private function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }


    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /supported_cities
     */
    public function getSupportedCities()
    {
        $response = array('cities' => $this->supported_cities);
        return $response;
    }
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /city_supported/$id
     */
    public function checkCitySupported($id = null)
    {
        $response = array('supported' => in_array($id, $this->supported_cities));
        return $response;
    }

    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /nearest_station/$lat/$lon/$city
     */
    public function getNearestStation($lat=null, $lon = null,$city=null)
    {
        $ch = curl_init($this->apis[$city]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $nearest_station = null;
        $nearest_distance = -1;

        if ($city == NANTES || $city == MARSEILLE)
        {
            $data = json_decode($data);
            $nearest_station = $data[0];
            for($i = 0; $i < count($data); $i++)
            {
                $station = $data[$i];
                $data[$i]->distance = $station_distance = $this->distance(strval($lat), strval($lon), strval($station->position->lat), strval($station->position->lng), "K");
                if (($station_distance < $nearest_distance
                    || $nearest_distance === -1)
                    && $station->status == "OPEN"
                    && $station->available_bikes > 0)
                {
                    unset($station->banking);
                    unset($station->contract_name);
                    unset($station->bonus);
                    unset($station->address);
                    unset($station->bike_stands);
                    unset($station->status);
                    $nearest_station = $station;
                    $nearest_distance = $station_distance;
                }
            }
            // Naming
            if ($city == NANTES) {
                $nearest_station->commercial_name = "Bicloo";
            } elseif ($city == MARSEILLE) {
                $nearest_station->commercial_name = "Le Vélo";
            }
        } elseif ($city == NICE) {
            $data = json_decode($data);
            $nearest_station = new BikeStation("Vélo Bleu");
            $stations = $data->stand;
            for($i = 0; $i < count($stations); $i++) {
                $station = $stations[$i];
                $station_distance = $this->distance($lat, $lon, $stations[$i]->lat, $stations[$i]->lng, "K");
                if (($station_distance < $nearest_distance || $nearest_distance === -1)
                    && $station->disp == 1
                    && $station->ab > 0) {
                    $nearest_station->setName(urldecode((empty($station->wcom)) ? $station->name : $station->wcom));
                    $nearest_station->setAvailableBikes(intval($station->ab));
                    $nearest_station->setAvailableBikeStands(intval($station->ap));
                    $nearest_station->setDistance($station_distance);
                    $nearest_station->setPosition(array("lat" => floatval($station->lat), "lng" => floatval($station->lng)));
                    $nearest_station->setNumber(intval($station->id));
                    $nearest_station->setLastUpdate(strtotime($data->gmt));
                    $nearest_distance = $station_distance;
                }
            }
        } elseif ($city == BORDEAUX) {
            $nearest_station = new BikeStation("Vcub");
            $xml = simplexml_load_string($data);
            $arrayData = xmlToArray($xml);

            //Conversion en objet PHP
            $stations = new stdClass();
            foreach ($arrayData as $key => $value)
            {
                $stations->$key = $value;
            }

            /** @var array|Countable $stations */
            $stations = $stations->{'FeatureCollection'}['gml:featureMember'];
            for($i = 0; $i < count($stations); $i++) {
                $station = $stations[$i]['ms:CI_VCUB_P'];
                $station_lat_lng = explode(' ', $station['ms:msGeometry']['gml:Point']["gml:pos"]);
                $station_lat = $station_lat_lng[0];
                $station_lng = $station_lat_lng[1];
                $station_distance = $this->distance($lat, $lon, $station_lat, $station_lng, "K");
                if (($station_distance < $nearest_distance || $nearest_distance === -1)
                    && $station["ms:ETAT"] == "CONNECTEE"
                    && intval($station['ms:NBVELOS']) > 0) {
                    $nearest_station->setName($station['ms:NOM']);
                    $nearest_station->setAvailableBikes(intval($station['ms:NBVELOS']));
                    $nearest_station->setAvailableBikeStands(intval($station['ms:NBPLACES']));
                    $nearest_station->setDistance($station_distance);
                    $nearest_station->setPosition(array("lat" => floatval($station_lat), "lng" => floatval($station_lng)));
                    $nearest_station->setNumber(intval($station['ms:GID']));
                    $nearest_station->setLastUpdate(strtotime($station['ms:HEURE']));
                    $nearest_distance = $station_distance;
                }
            }
        }
        return $nearest_station;
    }

}