<?php

namespace MyJobManagerBundle\Controller;

use MyJobManagerBundle\Entity\Paiment;
use Symfony\Component\Routing\Annotation\Route;
use MyJobManagerBundle\Entity\Bill;
use MyJobManagerBundle\Entity\BillLine;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyJobManagerBundle\Form\BillType;
use MyJobManagerBundle\Form\BillLine2Type;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BillController extends Controller
{
    /**
     * list the bills
     *
     * @Route("/bill", name="myjobmanager_bill_list")
     * @return Response list of bills if connected
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $list_bill = $this->mobileIndexAction();
        if ((is_int($list_bill)) && ($list_bill == -401)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        return $this->render('MyJobManagerBundle:Bill:index.html.twig', array(
            'list' => $list_bill,
            'user' => $user
        ));
    }

    /**
     * Get the Bill list data for myjobmanager app
     *
     * @Route("/mobile/bill", name="myjobmanager_mobile_bill_list")
     */
    public function mobileIndexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) return 401;
        $manager = $this->getDoctrine()->getManager();
        $list_bill = $manager->getRepository('MyJobManagerBundle:Bill')->findByRefUser($user);
        return $list_bill;
    }

    /**
     * print full view of a bill, add line if post
     *
     * @Route("/bill/{id}", name="myjobmanager_bill_view", requirements={"id" = "\d+"});
     * @param Request $req post request of line creation if any
     * @param int $id bill id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function viewAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $bill = $this->mobileViewAction($id);
        if (is_int($bill)) {
            if ($bill == -401) {
                $ar401 = ["unauthorized"];
                return new Response(json_encode($ar401), 401);
            }
            if ($bill == -404) return $this->redirect($this->generateUrl("myjobmanager_bill_list"));
        }
        $user = $this->getUser();
        $line = new BillLine();
        $form = $this->get('form.factory')->create(BillLine2Type::class, $line);
        if (($req->isMethod('POST')) && ($bill->getStatus() == "draft")) {
            if ($form->handleRequest($req)->isValid()) {
                $bill->addLine($line);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($line);
                $manager->flush();
                $req->getSession()->getFlashBag()->add('notice', 'Ligne enregistrée');
                return $this->redirect($this->generateUrl("myjobmanager_bill_view", array('id' => $bill->getId())));
            }
        }
        return $this->render('MyJobManagerBundle:Bill:full.html.twig', array(
            'user' => $user,
            'bill' => $bill,
            'form' => $form->createView()
        ));
    }

    /**
     * Get the Bill list data for myjobmanager app
     *
     * @Route("/mobile/bill/{id}", name="myjobmanager_mobile_bill_view", requirements={"id" = "\d+"});
     * @param int $id bill id
     * @return int|object
     */
    public function mobileViewAction(int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) return -401;
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $bill = $manager->getRepository('MyJobManagerBundle:Bill')->find($id);

            if ($bill->getRefUser() != $user) return -401;

            if ($bill != NULL) {
                return $bill;
            }
        }
        return -404;
    }

    /**
     * create a bill if post request
     * else return the form to create one
     *
     * @Route("/bill/add", name="myjobmanager_bill_add")
     * @param Request $req post request from the creation form
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addAction(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();

        //si pas de client, renvoie à la création de client
        $clients = $manager->getRepository('MyJobManagerBundle:Client')->findByUserRef($user);
        if (empty($clients)) {
            return $this->redirectToRoute("myjobmanager_clients_add");
        }
        $bill = new Bill();
        $form = $this->createForm(BillType::class, $bill, array('uid' => $user->getId()));
        if ($req->isMethod('POST')) {
            if (($form->handleRequest($req)->isValid()) && ($bill->getClient() != NULL)) {
                $bill->setRefUser($user);
                $manager->persist($bill);
                $manager->flush();
                $req->getSession()->getFlashBag()->add('notice', 'Facture enregistrée');
                return $this->redirect($this->generateUrl("myjobmanager_bill_view", array('id' => $bill->getId())));
            }
        }
        return $this->render('MyJobManagerBundle:Bill:add.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Route("/bill/{id}/add", name="myjobmanager_bill_add_line")
     * @param Request $req
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addLineAction(Request $req, $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $bill = $manager->getRepository('MyJobManagerBundle:Bill')->find($id);
            if ($bill != NULL) {
                if ($bill->getRefUser() != $user) {
                    $ar401 = ["not your bill"];
                    return new Response(json_encode($ar401), 401);
                }
                $line = new BillLine();
                $form = $this->get('form.factory')->create(BillLine2Type::class, $line);
                if ($req->isMethod('POST')) {
                    if ($form->handleRequest($req)->isValid()) {
                        $bill->addLine($line);
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($line);
                        $manager->flush();
                        $req->getSession()->getFlashBag()->add('notice', 'Ligne enregistrée');
                        return $this->redirect($this->generateUrl("myjobmanager_bill_view", array('id' => $bill->getId())));
                    }
                }
                return $this->render('MyJobManagerBundle:Bill:addline2.html.twig', array(
                    'form' => $form->createView(),
                    'bill' => $bill,
                    'user' => $user
                ));
            }
        }
        return $this->redirect($this->generateUrl("myjobmanager_bill_list"));
    }

    /**
     * @Route("/bill/{id}/{line}/delete", name="myjobmanager_bill_delete_line")
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
            $bill = $manager->getRepository('MyJobManagerBundle:Bill')->find($id);
            if ($bill != NULL) {
                if ($bill->getRefUser() != $user) {
                    $ar401 = ["not your bill"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                if ($bill->getStatus() != "draft")
                    return $this->redirect($this->generateUrl("myjobmanager_bill_view", array('id' => $bill->getId())));
                $lines = $bill->getLines();
                foreach ($lines as $line_elt)
                {
                    if ($line_elt->getId() == $line)
                    {
                        $bill->removeLine($line_elt);
                        $manager->persist($bill);
                        $manager->flush();
                    }
                }
                return $this->redirect($this->generateUrl("myjobmanager_bill_view", array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl("myjobmanager_bill_list"));
    }

    /**
     * set a new status, refered by $status to a bill, refered by id
     *
     * @Route("/bill/{id}/status/{status}", name="myjobmanager_bill_status_set")
     * @param int $id bill id
     * @param string $status new status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response bill view if found out, else list bills
     */
    public function statusSetAction(int $id, string $status)
    {
        $user = $this->getUser();
        if (!is_object($user))
            throw new AccessDeniedException('Non connecté');
        if ($id > 0)
        {
            $manager = $this->getDoctrine()->getManager();
            $bill = $manager->getRepository('MyJobManagerBundle:Bill')->find($id);
            if ($bill != NULL) {
                if ($bill->getRefUser() != $user) {
                    $ar401 = ["not your bill"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                if (in_array($status, ["canceled", "sent", "draft"])) {
                    $bill->setStatus($status);
                    if ($status == "sent")
                        $bill->setSendTime(new \DateTime());
                    $manager->flush();
                }
                return $this->redirect($this->generateUrl("myjobmanager_bill_view", array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl("myjobmanager_bill_list"));
    }

    /**
     * add a paiment (from a post request) to a bill, refered by $id
     *
     * @Route("/bill/{id}/add/paiment", name="myjobmanager_bill_add_paiment")
     * @param int $id bill id
     * @param Request $req post, paiment creation form
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response bill view if found out, else list bills
     */
    public function addPaimentToBillAction (Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $bill = $manager->getRepository('MyJobManagerBundle:Bill')->find($id);
            if ($bill != NULL) {
                if ($bill->getRefUser() != $user) {
                    $ar401 = ["not your bill"];
                    return new Response(json_encode($ar401), 401);
                }
                $paiment = new Paiment();
                $paiment->setAmount($req->get('amount'));
                $paiment->setMode($req->get('mode'));
                $paiment->setDatePaiment(new \DateTime());
                $paiment->setRefUser($user);
                $paiment->addRefBill($bill);
                $bill->addRefPaiment($paiment);
                if ($bill->getSumPaiments() + $req->get('amount') >= $bill->getSumTTC())
                    $bill->setStatus("paid");
                else
                    $bill->setStatus("partially");
                $manager->persist($paiment);
                $manager->flush();
                return $this->redirect($this->generateUrl("myjobmanager_bill_view", array('id' => $bill->getId())));
            }
        }
        return $this->redirect($this->generateUrl("myjobmanager_bill_list"));
    }
}