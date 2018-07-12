<?php

/**
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 */

namespace BillAndGoBundle\Controller;

use AppBundle\Service\SuggestionService;
use BillAndGoBundle\Entity\Suggestion;
use BillAndGoBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SuggestionController
 * @package BillAndGoBundle\Controller
 */
class SuggestionController extends Controller
{
    /** @var SuggestionService $suggestionService */
    private $suggestionService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->suggestionService = $this->get("billandgo.suggestion");
    }

    /**
     * JSON encoded list of suggestion, linked to user or common
     *
     * @Route("/suggestions/index/json", name="billandgo_suggestion_index_json")
     * @return Response
     */
    public function indexJSONAction(): Response
    {
        $user = $this->getUser();
        if (!($user instanceof User)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $list = $this->suggestionService->getList($user);
        $array = [];
        foreach ($list as $suggestion) {
            /** @var Suggestion $suggestion */
            $array[$suggestion->getName()] = [
                'name'          => $suggestion->getName(),
                'description'   => $suggestion->getDescription(),
                'priceHT'       => $suggestion->getPriceHT(),
                'time'          => $suggestion->getTime()
            ];
        }
        $json = json_encode($array);
        return new Response($json, 200);
    }
}
