<?php

/**
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://www.billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 */


namespace BillAndGoBundle\Controller;

use BillAndGoBundle\Entity\Tax;
use BillAndGoBundle\BillAndGoBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class TaxController extends Controller
{
    /**
     * Lists all tax entities.
     *
     * @Route("/tax/index", name="billandgo_tax_index")
     * @return              \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(): Response
    {
        $user = $this->getUser();
        if ($user->getId() != 1) {
            $ar401 = ["not admin"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $manager = $this->getDoctrine()->getManager();
        $taxes = $manager->getRepository('BillAndGoBundle:Tax')->findAll();

        return $this->render(
            'BillAndGoBundle:Tax:index.html.twig',
            array(
            'taxes' => $taxes,
            'user' => $user,
                'usersub' => $usersub
            )
        );
    }

    /**
     * Create all default taxes
     * Admin only
     *
     * @Route("/tax/create", name="billandgo_tax_create")
     * @return               \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(): Response
    {
        $user = $this->getUser();
        if ($user->getId() != 1) {
            $ar401 = ["not admin"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $manager = $this->getDoctrine()->getManager();
        $check = $manager->getRepository("BillAndGoBundle:Tax")->findByName("TVA taux normal");
        if (!($check)) {
            $tva20 = new Tax();
            $tva20->setName("TVA taux normal");
            $tva20->setPercent(20);
            $tva20->setHelp("concerne la plupart des biens et services");
            $manager->persist($tva20);
        }
        $check = $manager->getRepository("BillAndGoBundle:Tax")->findByName("TVA taux intermédiaire");
        if (!($check)) {
            $tva10 = new Tax();
            $tva10->setName("TVA taux intermédiaire");
            $tva10->setPercent(10);
            $tva10->setHelp("applicable à la restauration");
            $manager->persist($tva10);
        }
        $check = $manager->getRepository("BillAndGoBundle:Tax")->findByName("TVA taux réduit");
        if (!($check)) {
            $tva5 = new Tax();
            $tva5->setName("TVA taux réduit");
            $tva5->setPercent(5.5);
            $tva5->setHelp("applicable aux produits alimentaires, à l'énergie et aux livres");
            $manager->persist($tva5);
        }
        $check = $manager->getRepository("BillAndGoBundle:Tax")->findByName("TVA taux super réduit");
        if (!($check)) {
            $tva2 = new Tax();
            $tva2->setName("TVA taux super réduit");
            $tva2->setPercent(2.1);
            $tva2->setHelp("applicable aux médicaments et à la presse écrite");
            $manager->persist($tva2);
        }
        $check = $manager->getRepository("BillAndGoBundle:Tax")->findByName("Pas de TVA");
        if (!($check)) {
            $tva0 = new Tax();
            $tva0->setName("Pas de TVA");
            $tva0->setPercent(0);
            $tva0->setHelp("Franchise de TVA");
            $manager->persist($tva0);
        }
        $manager->flush();
        return $this->redirectToRoute("billandgo_tax_index");
    }


}
