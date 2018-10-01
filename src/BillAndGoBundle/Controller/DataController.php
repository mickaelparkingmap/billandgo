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


use BillAndGoBundle\Entity\Paiment;
use BillAndGoBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\DateTime;

class DataController extends Controller
{


    /**
     * @Route("/mes-donnees/perso", name="billandgo_datas_perso")
     */
    public function persoAction()
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
        $response = new StreamedResponse();
        $response->setCallback(function() use($user){

            $handle = fopen('php://output', 'w+');
            // Nom des colonnes du CSV
            fputcsv($handle, array(
                'Nom',
                'Prénom',
                'email',
                'Dernière connexion',
                'Société',
                'Adresse',
                'Code postal',
                'Ville',
                'Mobile',
                'Téléphone',
                'Siret',
                'Iban',
                'Banque',
                'Status',
            ),';');

            //Champs

            fputcsv($handle,array(
                $user->getLastname(),
                $user->getFirstname(),
                $user->getEmail(),
                ($user->getLastLogin())->format("d/m/Y H:i:s"),
                $user->getCompanyname(),
                $user->getAddress(),
                $user->getZipCode(),
                $user->getCity(),
                $user->getMobile(),
                $user->getPhone(),
                $user->getSiret(),
                $user->getIban(),
                $user->getBanque(),
                $user->getJobtype()
            ),';');


            fclose($handle);
        });

        $date = (new \DateTime())->format("d/m/Y-H-i-s");

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="mes-donnees-perso'.$date.'.csv"');

        return $response;
    }

    /**
     * @Route("/mes-donnees/paiement", name="billandgo_datas_payments")
     */
    public function paymentAction()
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

        $em = $this->getDoctrine()->getManager();
        $payments = $em->getRepository('BillAndGoBundle:Paiment')->findByRefUser($user);

        $response = new StreamedResponse();
        $response->setCallback(function() use($payments){

            $handle = fopen('php://output', 'w+');
            // Nom des colonnes du CSV
            fputcsv($handle, array(
                'Date',
                'Mode',
                'Prix',
            ),';');

            //Champs

            foreach ($payments as $one) {
                fputcsv($handle,array(
                    ($one->getDatePaiment())->format("d/m/Y"),
                    $one->getMode(),
                    $one->getAmount(),
                ),';');
            }
            fclose($handle);
        });

        $date = (new \DateTime())->format("d/m/Y-H-i-s");

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="mes-donnees-paiements'.$date.'.csv"');

        return $response;
    }


    /**
     * @Route("/mes-donnees/projets", name="billandgo_datas_projects")
     */
    public function projectsAction()
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

        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('BillAndGoBundle:Project')->findByRefUser($user);

        $response = new StreamedResponse();
        $response->setCallback(function() use($projects){

            $handle = fopen('php://output', 'w+');
            // Nom des colonnes du CSV
            fputcsv($handle, array(
                'Nom',
                'Début',
                'Fin',
                'Description',
                'Client',
            ),';');

            //Champs
            foreach ($projects as $one) {
                fputcsv($handle,array(
                    $one->getName(),
                    ($one->getBegin())->format("d/m/Y H:i:s"),
                    ($one->getDeadline())->format("d/m/Y H:i:s"),
                    $one->getDescription(),
                    $one->getRefClient()->getCompanyName()
                ),';');
            }
            fclose($handle);
        });

        $date = (new \DateTime())->format("d/m/Y-H-i-s");

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="mes-donnees-projets'.$date.'.csv"');

        return $response;
    }


    /**
     * @Route("/mes-donnees/supprimer-mon-compte", name="billandgo_datas_unsubscribe")
     */
     public function unsubscribeAction()
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

        $user->setUsername(md5($user->getUsername()).$user->getId());
        $user->setUsernameCanonical(md5($user->getUsernameCanonical()).$user->getId());
        $user->setEmail(md5($user->getEmail()).$user->getId());
        $user->setEmailCanonical(md5($user->getEmailCanonical()).$user->getId());
        $user->setEnabled(0);
        $user->setRoles([]);
        $user->setFirstname(md5($user->getFirstname()));
        $user->setLastname(md5($user->getLastname()));
        $user->setCompanyname(md5($user->getCompanyname()));
        $user->setAddress(md5($user->getAddress()));
        $user->setZipcode(md5($user->getZipcode()));
        $user->setCity(md5($user->getCity()));

        $user->setCountry(md5($user->getCountry()));
        $user->setMobile(md5($user->getMobile()));
        $user->setPhone(md5($user->getPhone()));
        $user->setSiret(md5($user->getSiret()));

        $user->setBanque(md5($user->getBanque()));
        $user->setIban(md5($user->getIban()));
        $user->setBic(md5($user->getBic()));
        $user->setUnsubscribe(99);
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($user);

        return ($this->redirect("/logout"));
    }
}
