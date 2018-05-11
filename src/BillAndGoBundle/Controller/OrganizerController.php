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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrganizerController extends Controller
{

    /**
     * @Route("/limited", name="billandgo_limitation")
     */
    public function limitedAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render(
            'BillAndGoBundle:Default:limited.html.twig', array(
            'user' => $user,
            )
        );
    }

    /**
     * @Route("/agenda", name="billandgo_organizer_show")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        return $this->render(
            'BillAndGoBundle:Organizer:index.html.twig', array(
            'user' => $user
            )
        );
    }

    public function getLimitation($type) 
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $manager = $this->getDoctrine()->getManager();
        $projects = ($manager->getRepository('BillAndGoBundle:Project')->findByRefUser($user));
        $bills = ($manager->getRepository('BillAndGoBundle:Document')->findAllBill($user->getId()));
        $quotes = ($estimates = $manager->getRepository('BillAndGoBundle:Document')->findAllEstimate($user->getId()));
        $clients = ($manager->getRepository('BillAndGoBundle:Client')->findByUserRef($user));
        if ($user->getPlan() != "billandgo_paid_plan") {
            switch ($type) {
            case 'project' :
                if (count($projects) >= 15) {
                    return (false);
                }
                return (true);
                    break;
            case 'bill' :
                if (count($bills) >= 15) {
                    return (false);
                }
                return (true);
                    break;
            case 'quote' :
                if (count($quotes) >= 15) {
                    return (false);
                }
                return (true);
                    break;
            case 'client' :
                if (count($clients) >= 15) {
                    return (false);
                }
                return (true);
                    break;
            }
        }
        return (true);
    }
}
