<?php

namespace ApiBundle\Controller;

use ApiBundle\Service\ApiAdapter;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 * @Route("/api", service="api.api_controller")
 */
class ApiController extends Controller
{
    /**
     * The apiAdapter.
     *
     * @var ApiAdapter
     */
    protected $apiAdapter;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * ApiController constructor.
     *
     * @param ApiAdapter          $apiAdapter
     * @param SerializerInterface $serializer
     */
    public function __construct(ApiAdapter $apiAdapter, SerializerInterface $serializer)
    {
        $this->apiAdapter = $apiAdapter;
        $this->serializer = $serializer;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Returns all available cities",
     *     tags={
     *         "stable",
     *     },
     *     output="json"
     * )
     *
     * @Rest\Get("/cities")
     */
    public function getAvailableCitiesAction()
    {
        $response = $this->apiAdapter->getAvailableCities();
        $response = $this->serializer->serialize($response, 'json');

        return new JsonResponse($response);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Returns the nearest station of your current position",
     *     requirements={
     *          {
     *              "name"="city",
     *              "dataType"="string",
     *              "requirement"="\s",
     *              "description"="The city where you are"
     *          },
     *          {
     *              "name"="latitude",
     *              "dataType"="float",
     *              "requirement"="\f",
     *              "description"="Your current latitude"
     *          },
     *          {
     *              "name"="longitude",
     *              "dataType"="float",
     *              "requirement"="\f",
     *              "description"="Your current longitude"
     *          }
     *      },
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when the city is not available"
     *     },
     *     tags={
     *         "stable",
     *     }
     * )
     *
     * @Rest\Get("/nearest_station/{city}/{latitude}/{longitude}")
     */
    public function getNearestStationAction($city, $latitude, $longitude)
    {
        try {
            $response = $this->apiAdapter->getNearestStation($latitude, $longitude, $city);
            $response =  $this->serializer->serialize($response, 'json');

            return new JsonResponse($response);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }
}
