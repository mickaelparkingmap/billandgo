<?php

namespace MyJobManagerBundle\Controller;

use MyJobManagerBundle\Entity\Devis;
use MyJobManagerBundle\Entity\DevisLine;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use MyJobManagerBundle\Form\DevisType;
use MyJobManagerBundle\Form\DevisEditType;
use MyJobManagerBundle\Form\DevisLine2Type;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class DevisController extends Controller
{

    /**
     * @Route("/devis", name="myjobmanager_devis_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $list_devis = $this->mobileIndexAction();
        if ((is_int($list_devis)) && ($list_devis == -401)) {
            $ar401 = ["not connected"];
            return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
        }
        return $this->render('MyJobManagerBundle:Devis:index.html.twig', array(
            'list' => $list_devis,
            'user' => $user
        ));
    }

    /**
     * @Route("/mobile/devis", name="myjobmanager_mobile_devis_list")
     */
    public function mobileIndexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) return 401;
        $manager = $this->getDoctrine()->getManager();
        $list_devis = $manager->getRepository('MyJobManagerBundle:Devis')->findByRefUser($user);
        return $list_devis;
    }

    /**
     * @Route("/devis/{id}", name="myjobmanager_devis_view", requirements={"id" = "\d+"});
     * @param Request $req
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $devis = $this->mobileViewAction($id);
        if (is_int($devis)) {
            if ($devis == -401) {
                $ar401 = ["unauthorized"];
                return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
            }
            if ($devis == -404) return $this->redirect($this->generateUrl("myjobmanager_devis_list"));
        }
        $line = new DevisLine();
        $form = $this->get('form.factory')->create(DevisLine2Type::class, $line);
        $formEdit = $this->get('form.factory')->create(DevisEditType::class, $devis);
        if (($req->isMethod('POST')) && ($devis->getStatus() == "draft")) {
            if ($form->handleRequest($req)->isValid()) {
                $devis->addLine($line);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($line);
                $manager->flush();
                $req->getSession()->getFlashBag()->add('notice', 'Ligne enregistrée');
                return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $devis->getId())));
            }
            else if ($formEdit->handleRequest($req)->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($devis);
                $manager->flush();
                return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $devis->getId())));
            }
        }
        return $this->render('MyJobManagerBundle:Devis:full.html.twig', array(
            'devis' => $devis,
            'form' => $form->createView(),
            'formEdit' => $formEdit->createView(),
            'user' => $user
        ));
    }

    /**
     * @Route("/mobile/devis/{id}", name="myjobmanager_mobile_devis_view", requirements={"id" = "\d+"});
     * @param int $id
     * @return int|object|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function mobileViewAction(int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) return -401;
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $devis = $manager->getRepository('MyJobManagerBundle:Devis')->find($id);
            if ($devis->getRefUser() != $user) return -401;
            if ($devis != NULL) {
                return $devis;
            }
        }
        return -404;
    }

    /**
     * @Route("/devis/add", name="myjobmanager_devis_add")
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $manager = $this->getDoctrine()->getManager();

        //si pas de client, renvoie à la création de client
        $clients = $manager->getRepository('MyJobManagerBundle:Client')->findByUserRef($user);
        if (empty($clients)) {
            return $this->redirectToRoute("myjobmanager_clients_add");
        }
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis, array('uid' => $user->getId()));
        if ($req->isMethod('POST')) {
            if (($form->handleRequest($req)->isValid()) && ($devis->getClient() != NULL)) {
                $devis->setRefUser($user);
                $manager->persist($devis);
                $manager->flush();
                $req->getSession()->getFlashBag()->add('notice', 'Devis enregistré');
                return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $devis->getId())));
            }
        }
        return $this->render('MyJobManagerBundle:Devis:add.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Route("/devis/{id}/add", name="myjobmanager_devis_add_line")
     * @param Request $req
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addLineAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $devis = $manager->getRepository('MyJobManagerBundle:Devis')->find($id);
            if ($devis != NULL) {
                if ($devis->getRefUser() != $user) {
                    $ar401 = ["not your estimate"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                if ($devis->getStatus() != "draft")
                    return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $devis->getId())));
                $line = new DevisLine();
                $form = $this->get('form.factory')->create(DevisLine2Type::class, $line);
                if ($req->isMethod('POST')) {
                    if ($form->handleRequest($req)->isValid()) {
                        $devis->addLine($line);
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($line);
                        $manager->flush();
                        $req->getSession()->getFlashBag()->add('notice', 'Ligne enregistrée');
                        return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $devis->getId())));
                    }
                }
                return $this->render('MyJobManagerBundle:Devis:addline2.html.twig', array(
                    'form' => $form->createView(),
                    'devis' => $devis,
                    'user' => $user
                ));
            }
        }
        return $this->redirect($this->generateUrl("myjobmanager_devis_list"));
    }

    /**
     * @Route("/devis/{id}/{line}/delete", name="myjobmanager_devis_delete_line")
     * @param int $id, $line
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteLineAction(int $id, $line)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $devis = $manager->getRepository('MyJobManagerBundle:Devis')->find($id);
            if ($devis != NULL) {
                if ($devis->getRefUser() != $user) {
                    $ar401 = ["not your estimate"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                if ($devis->getStatus() != "draft")
                    return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $devis->getId())));
                $lines = $devis->getLines();
                foreach ($lines as $line_elt)
                {
                    if ($line_elt->getId() == $line)
                    {
                        $devis->removeLine($line_elt);
                        $manager->persist($devis);
                        $manager->flush();
                    }
                }
                return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl("myjobmanager_devis_list"));
    }

    /**
     * @Route("/devis/{id}/status/{status}", name="myjobmanager_devis_status_set")
     * @param int $id, string $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function statusSetAction(int $id, string $status)
    {
        $user = $this->getUser();
        if (!is_object($user))
            throw new AccessDeniedException('Non connecté');
        if ($id > 0)
        {
            $manager = $this->getDoctrine()->getManager();
            $estimate = $manager->getRepository('MyJobManagerBundle:Devis')->find($id);
            if ($estimate != NULL) {
                if ($estimate->getRefUser() != $user) {
                    $ar401 = ["not your estimate"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                if (in_array($status, ["canceled", "sent", "refused", "draft", "accepted"])) {
                    $estimate->setStatus($status);
                    if ($status == "sent")
                        $estimate->setSendTime(new \DateTime());
                    if (($status == "refused") || ($status == "accepted"))
                        $estimate->setResponseTime(new \DateTime());
                    $manager->flush();
                }
                return $this->redirect($this->generateUrl("myjobmanager_devis_view", array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl("myjobmanager_devis_list"));
    }

}