<?php

namespace BillAndGoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{

   /* public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        return ($this->redirect("/bill"));
        //return $this->render('BillAndGoBundle:Default:index.html.twig');
    }*/

    /**
     * @Route("/", name="billandgo_dashboard")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $manager = $this->getDoctrine()->getManager();
        $projects = ($manager->getRepository('BillAndGoBundle:Project')->findByRefUser($user));
        $bills = count($manager->getRepository('BillAndGoBundle:Document')->findAllBill($user->getId()));
        $quotes = count($estimates = $manager->getRepository('BillAndGoBundle:Document')->findAllEstimate($user->getId()));
        $clients = count($manager->getRepository('BillAndGoBundle:Client')->findByUserRef($user));
        return $this->render('BillAndGoBundle:Default:dashboard.html.twig', array(
            'user' => $user,
            'project' => count($projects),
            'projects' => $projects,
            'bills' => $bills,
            'quotes' => $quotes,
            'clients' => $clients
        ));
    }
}
