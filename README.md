# Usage :
`composer install`  
The API will be available in the subdirectory you put it.  

# Available calls
At the moment, there is only 1 `GET` call available :
`GET /nearest_station/$lat/$lon/$city`  
  
*Parameters :*
- $lat : the latitude of the coordinates to search the nearest station from  
- $lon : the longitude of the coordinates to search the nearest station from  
- $city : the city identifier, as defined in `/helpers/Constants.php` 
