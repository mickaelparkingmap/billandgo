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

use BillAndGoBundle\Entity\UserOption;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Tests\Controller\ContainerAwareController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    /**
     * Legal mention page
     * @Route("/mentions-legales", name="billandgo_ml")
     */
    public function mlAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $legal = $manager->getRepository('BillAndGoBundle:LegalTerms')->lastLegal();
        return $this->render(
            'BillAndGoBundle:Default:ml.html.twig', ["legal" => $legal]
        );
    }

    /**
     * User data page
     * @Route("/mes-donnees/", name="billandgo_datas")
     */
    public function dataAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }

        return $this->render(
            'BillAndGoBundle:Default:datas.html.twig',
            array(
                'user' => $user,
                'usersub' => $usersub
            )
        );
    }

    /**
     * @Route("/", name="billandgo_dashboard")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }

        $manager = $this->getDoctrine()->getManager();
        $projects = ($manager->getRepository('BillAndGoBundle:Project')->findByRefUser($user));
        $bills = ($manager->getRepository('BillAndGoBundle:Document')->findAllBill($user->getId()));
        $repoDoc = $manager->getRepository('BillAndGoBundle:Document');
        $quotes = ($estimates = $repoDoc->findAllEstimate($user->getId()));
        $paiments = $manager->getRepository('BillAndGoBundle:Paiment')->findByRefUser($user);
        $billpaidm = 0;
        $billtotalm = 0;
        $quotesacceptm = 0;
        $quotestotalm = 0;
        $now = new \DateTime();
        $now = $now->format('m-Y');

        foreach ($bills as $one) {
            if (null != $one->getAnswerDate()) {
                $n = $one->getAnswerDate()->format('m-Y');
            } else {
                $n = null;
            }

            if ($one->isBillPaid() && $n == $now) {
                $billpaidm++;
            }
            $billtotalm++;
        }


        foreach ($quotes as $one) {
            if (null != $one->getAnswerDate()) {
                $n = $one->getAnswerDate()->format('m-Y');
            } else {
                $n = null;
            }

            if ($one->getStatus() == "accepted" && $n == $now) {
                $quotesacceptm++;
            }
            $quotestotalm++;
        }

        $cb = ($repoDoc->getBillOrQuote((new \DateTime())->format("Y"), 0, $user->getId(),
            $this->getDoctrine()->getConnection()));
        $cq = ($repoDoc->getBillOrQuote((new \DateTime())->format("Y"), 1, $user->getId(),
            $this->getDoctrine()->getConnection()));


        $clients = count($manager->getRepository('BillAndGoBundle:Client')->findByUserRef($user));

        return $this->render(
            'BillAndGoBundle:Default:dashboard.html.twig',
            array(
                'user' => $user,
                'project' => count($projects),
                'projects' => $projects,
                'bills' => count($bills),
                'quotes' => count($quotes),
                'clients' => $clients,
                'billpaidm' => $billpaidm,
                'billtotalm' => $billtotalm,
                'quotestotalm' => $quotestotalm,
                'quotesacceptm' => $quotesacceptm,
                'paiments' => $paiments,
                "usersub" => $usersub,
                'enddate' => date("t/m/Y", strtotime((new \DateTime())->format('Y-m-d'))),
                "cb" => $cb, "cq" => $cq
            )
        );
    }

    public static function userSubscription(UserInterface $user, ContainerAwareInterface $containerAware) {

        $manager = $containerAware->getDoctrine()->getManager();
        $planFree = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "user_free_plan"));

        $planPaid = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "user_paid_plan"));

        $current = new \DateTime();
        $plan = "free";
        $end = $current;
        $remaining = 0;
        $msg = "OK";

        if (null == $planPaid && null != $planFree) {
            $planFreeEnd = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
                array("user" => $user->getId(), "name" => "user_free_plan_end"));

            $end = \DateTime::createFromFormat('Y-m-d H:i:s', $planFreeEnd->getValue());
            $remaining  = $current->diff($end);
            $remaining = $remaining->format('%R%a');
            if ($remaining <= 0) {
                $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
                $containerAware->get('security.token_storage')->setToken($anonToken);
                $msg = "Votre version d'essai a expiré. Veuillez contacter le service Bill&Go".
                    " afin de souscrire à un abonnement. <a href='http://billandgo.fr'>Contactez-nous</a>";
                $response = new \Symfony\Component\HttpFoundation\Response();

                $response->setStatusCode(200);
                $response->headers->set('Refresh', 'url='.
                    $containerAware->generateUrl("fos_user_security_login"));

                $response->send();

            }
        }
        elseif (null != $planPaid) {
            $plan = "paid";
            $planPaidEnd = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
                array("user" => $user->getId(), "name" => "user_paid_plan_end"));

            $end = \DateTime::createFromFormat('Y-m-d H:i:s', $planPaidEnd->getValue());
            $remaining  = $current->diff($end);
            $remaining = $remaining->format('%R%a');
            if ($remaining <= 0) {
                $anonToken = new AnonymousToken('theTokensKey', 'anon.', array());
                $containerAware->get('security.token_storage')->setToken($anonToken);
                $msg = "Votre abonnement a expiré. Veuillez contacter le service Bill&Go afin de le".
                    " renouveller. <a href='http://billandgo.fr'>Contactez-nous</a>";
                $response = new \Symfony\Component\HttpFoundation\Response();

                $response->setStatusCode(200);
                $response->headers->set('Refresh', 'url='.
                    $containerAware->generateUrl("fos_user_security_login"));

                $response->send();

            }
        }
        else if (null == $planPaid && null == $planFree) {
            $manager = $containerAware->getDoctrine()->getManager();
            $us = new UserOption();
            $us->setUser($user);
            $us->setName("pdf_bill_quote_choice");
            $us->setValue("pdf.document.type.1");
            $manager->persist($us);
            $manager->flush();

            $us1 = new UserOption();
            $us1->setUser($user);
            $us1->setName("user_free_plan");
            $us1->setValue("active");
            $manager->persist($us1);
            $manager->flush();


            $dateNow = new \DateTime();
            $us2 = new UserOption();
            $us2->setUser($user);
            $us2->setName("user_free_plan_start");
            $us2->setValue(($dateNow)->format("Y-m-d H:i:s"));
            $manager->persist($us2);
            $manager->flush();


            $dateEdited = $dateNow->modify('+30 days');
            $us3 = new UserOption();
            $us3->setUser($user);
            $us3->setName("user_free_plan_end");
            $us3->setValue(($dateEdited)->format("Y-m-d H:i:s"));
            $manager->persist($us3);
            $manager->flush();
        }
        return (["plan" => $plan, "end" => $end, "remaining" => $remaining, "msg" => $msg]);
    }


}
