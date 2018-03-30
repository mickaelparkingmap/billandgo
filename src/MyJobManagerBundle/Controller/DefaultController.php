<?php

namespace MyJobManagerBundle\Controller;

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
        //return $this->render('MyJobManagerBundle:Default:index.html.twig');
    }*/

    /**
     * @Route("/", name="myjobmanager_dashboard")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('MyJobManagerBundle:Default:dashboard.html.twig', array(
            'user' => $user
        ));
    }
}
