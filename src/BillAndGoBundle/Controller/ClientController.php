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

use AppBundle\Service\ClientService;
use BillAndGoBundle\Entity\Client;
use BillAndGoBundle\Entity\ClientContact;
use BillAndGoBundle\Form\ClientContact2Type;
use BillAndGoBundle\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientController extends Controller
{
    /** @var ClientService */
    private $clientService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->clientService = $this->get("AppBundle\Service\ClientService");
    }

    /**
     * @Route("/clients", name="billandgo_clients_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $clients = $this->clientService->getClientListFromUser($user);
        return $this->render(
            'BillAndGoBundle:Client:index.html.twig',
            array(
            'list' => $clients,
            'user' => $user,
            'limitation' =>  $this->getLimitation("client")
            )
        );
    }

    /**
     * @Route("/clients/{id}", name="billandgo_clients_view", requirements={"id" = "\d+"});
     * @param Request $req
     * @param int     $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $client = $this->clientService->getClient($user, $id);

        $contact = new ClientContact();
        $form = $this->get('form.factory')->create(ClientContact2Type::class, $contact);
        if ($req->isMethod('POST')) {
            if ($form->handleRequest($req)->isValid()) {
                $client->addContact($contact);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($contact);
                $manager->flush();
                $req->getSession()->getFlashBag()->add('notice', 'Contact enregistré');
                return $this->redirect($this->generateUrl("billandgo_clients_view", array('id' => $client->getId())));
            }
        }
        return $this->render(
            'BillAndGoBundle:Client:full.html.twig',
            array(
            'client' => $client,
            'form' => $form->createView(),
            'user' => $user
            )
        );
    }

    /**
     * @Route("/clients/add", name="billandgo_clients_add")
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        if (false === $this->getLimitation("client")) {
            return $this->redirect($this->generateUrl("billandgo_limitation"));
        }

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        if ($req->isMethod('POST')) {
            if ($form->handleRequest($req)->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $client->setUserRef($user);
                $manager->persist($client);
                $manager->flush();
                $req->getSession()->getFlashBag()->add('notice', 'Client enregistré');
                return $this->redirect($this->generateUrl("billandgo_clients_view", array('id' => $client->getId())));
            }
        }
        return $this->render(
            'BillAndGoBundle:Client:add.html.twig',
            array(
            'form' => $form->createView(),
            'user' => $user
            )
        );
    }

    /**
     * @Route("/clients/{id}/add", name="billandgo_clients_add_contact")
     * @param Request $req
     * @param int     $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addContactAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) { // || !$user instanceof UserInterface
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $client = $manager->getRepository('BillAndGoBundle:Client')->find($id);
            if ($client != null) {
                if ($client->getUserRef() != $user) {
                    $ar401 = ["not your client"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                $contact = new ClientContact();
                $form = $this->get('form.factory')->create(ClientContact2Type::class, $contact);
                if ($req->isMethod('POST')) {
                    if ($form->handleRequest($req)->isValid()) {
                        $client->addContact($contact);
                        //$contact->setClientRef($client);
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($contact);
                        $manager->flush();
                        $req->getSession()->getFlashBag()->add('notice', 'Contact enregistré');
                        return $this->redirect($this->generateUrl("billandgo_clients_list", array('id' => $client->getId())));
                    }
                }
                return $this->render(
                    'BillAndGoBundle:Client:addcontact.html.twig',
                    array(
                    'form' => $form->createView(),
                    'client' => $client,
                    'user' => $user
                    )
                );
            }
        }
        return $this->redirect($this->generateUrl("billandgo_clients_list"));
    }

    /**
     * @Route("/client/{id}/{contact}/delete", name="billandgo_client_delete_contact")
     * @param $id, $contact
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteContactAction($id, $contact)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $client = $manager->getRepository('BillAndGoBundle:Client')->find($id);
            if ($client != null) {
                if ($client->getUserRef() != $user) {
                    $ar401 = ["not your client"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                $contacts = $client->getContacts();
                foreach ($contacts as $contact_elt) {
                    if ($contact_elt->getId() == $contact) {
                        $client->removeContact($contact_elt);
                        $manager->persist($client);
                        $manager->flush();
                    }
                }
                return $this->redirect($this->generateUrl("billandgo_clients_view", array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl("billandgo_clients_list"));
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
                case 'project':
                    if (count($projects) >= 15) {
                        return (false);
                    }
                    return (true);
                    break;
                case 'bill':
                    if (count($bills) >= 15) {
                        return (false);
                    }
                    return (true);
                    break;
                case 'quote':
                    if (count($quotes) >= 15) {
                        return (false);
                    }
                    return (true);
                    break;
                case 'client':
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
