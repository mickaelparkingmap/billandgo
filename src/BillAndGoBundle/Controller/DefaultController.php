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

use BillAndGoBundle\Entity\Client;
use BillAndGoBundle\Entity\Document;
use BillAndGoBundle\Entity\Project;
use BillAndGoBundle\Entity\UserOption;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Tests\Controller\ContainerAwareController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{

    private $base = array(
        ["name" => "Mes devis", "url" => "billandgo_estimate_index", "type" => "base",
            "summary" => "Accéder à vos devis"],
        ["name" => "Mes facture", "url" => "billandgo_bill_index", "type" => "base",
            "summary" => "Accéder à vos factures"],
        ["name" => "Mes paiements", "url" => "billandgo_paiment_index", "type" => "base",
            "summary" => "Accéder à vos paiements"],
        ["name" => "Ajouter un paiement", "url" => "billandgo_paiment_add", "type" => "base",
            "summary" => "Ajouter un nouveau paiement"],
        ["name" => "Ajouter un projet", "url" => "billandgo_project_add", "type" => "base",
            "summary" => "Ajouter un nouveau projet"],
        ["name" => "Mes projets", "url" => "billandgo_project_list", "type" => "base",
            "summary" => "Accéder à vos projets"],
        ["name" => "Editer mon profil", "url" => "fos_user_profile_edit", "type" => "base",
            "summary" => "Vous pouvez éditer votre profil"],
        ["name" => "Mon profil", "url" => "fos_user_profile_show", "type" => "base",
            "summary" => "Accéder à vos projets"],
        ["name" => "Paramètres de votre agenda", "url" => "billandgo_parameters_show",
            "type" => "base", "summary" => "Editer les options de votre agenda"],
        ["name" => "Modèle de PDF", "url" => "billandgo_parameters_show", "type" => "base",
            "summary" => "Editer le modèle de vos factures, devis"],
        ["name" => "Template PDF", "url" => "billandgo_parameters_show", "type" => "base",
            "summary" => "Editer le template de vos factures, devis"],
        ["name" => "Mon agenda", "url" => "billandgo_organizer_show", "type" => "base",
            "summary" => "Accéder à votre agenda"],
        ["name" => "Mes données", "url" => "billandgo_datas", "type" => "base",
            "summary" => "Accéder à vos données"],
        ["name" => "Désinscription", "url" => "billandgo_datas", "type" => "base",
            "summary" => "Désinscrivez-vous du service Bill&Go"],
        ["name" => "Exporter des données", "url" => "billandgo_datas", "type" => "base",
            "summary" => "Exporter vos données"],
        ["name" => "Mes clients", "url" => "billandgo_clients_list", "type" => "base",
            "summary" => "Accéder à la liste de vos clients"],
        ["name" => "Ajouter un client", "url" => "billandgo_clients_add", "type" => "base",
            "summary" => "Ajouter un nouveau client"],
    );

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

    /**
     * @Route("/search-global", name="billandgo_search_global")
     * @Method({"POST"})
     */
    public function searchGlobalAction(Request $request)
    {
        $vals = [];
        $q = $request->get("q");
        if (in_array($q, [null, ""])) {
            return (new JsonResponse(["results" => null]));
        }

        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            return (new JsonResponse(500, ["results" => []]));
        }

        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            return (new JsonResponse(500, ["results" => []]));
        }

        $vals = $this->arrayFind($q , $this->base);

        $repoDoc = $this->getDoctrine()->getRepository(Document::class);
        $docs = $repoDoc->findAllDoc($user->getId(), $q);
        foreach ($docs as $one) {
            $vals[] = ["name" => (($one->isEstimate())? "Devis" : "Facture" )." : ".$one->getNumber(),
                "url" => $this->generateUrl("billandgo_document_view", ["id" => $one->getId()]),
                "type" => "database",
                "summary" => $this->reduceText($one->getDescription(), 100)];
        }

        $repoProj = $this->getDoctrine()->getRepository(Project::class);
        $proj = $repoProj->findAllProject($user->getId(), $q);
        foreach ($proj as $one) {
            $vals[] = ["name" => "Projet : ".$one->getName() ,
                "url" => $this->generateUrl("billandgo_project_view",
                    ["id" => $one->getId()]), "type" => "database",
                "summary" => $this->reduceText($one->getDescription(), 100)];
        }


        $repoCli = $this->getDoctrine()->getRepository(Client::class);
        $cli = $repoCli->findAllClient($user->getId(), $q);
        foreach ($cli as $one) {
            $vals[] = ["name" => "Client : ".$one->getCompanyname() ,
                "url" => $this->generateUrl("billandgo_clients_view",
                    ["id" => $one->getId()]), "type" => "database",
                "summary" => $this->reduceText($one->getAdress().", ".$one->getZipcode()." ".
                    $one->getCity(), 100)];
        }

        return (new JsonResponse(["results" => $vals]));

    }

    /**
     * @param $needle
     * @param array $haystack
     * @return array
     */
    private function arrayFind($needle, array $haystack)
    {
        $elems = [];
        foreach ($haystack as $key => $value) {
            if (false !== stripos($value["name"], $needle) || false !== stripos($value["summary"], $needle)) {
                if ("base" === $value["type"]) {
                    $value["url"] = $this->generateUrl($value["url"]);
                }

                $elems[] = $value;
            }
        }
        return ($elems);
    }

    /**
     * @param string $str
     * @param int $limit
     * @return string
     */
    private function reduceText(string $str, int $limit) {
        if (strlen($str) <= $limit) {
            return $str;
        }
        $string = substr($str, 0, $limit);
        if (false !== ($breakpoint = strrpos($string, " "))) {
            $string = substr($string, 0, $breakpoint);
        }

        return ($string ." ...");
    }


}
