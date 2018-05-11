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

use BillAndGoBundle\Entity\Document;
use BillAndGoBundle\Entity\Paiment;
use PhpParser\Comment\Doc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BillAndGoBundle\Form\PaimentType;
use BillAndGoBundle\Form\DocumentType;

/**
 * Paiment controller.
 *
 * @Route("paiments")
 */
class PaimentController extends Controller
{
    /**
     * Lists all paiment entities.
     *
     * @Route("/",    name="billandgo_paiment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $em = $this->getDoctrine()->getManager();
        $paiments = $em->getRepository('BillAndGoBundle:Paiment')->findByRefUser($user);
        return $this->render(
            'BillAndGoBundle:Paiment:index.html.twig', array(
            'paiments' => $paiments,
            'user' => $user
            )
        );
    }

    /**
     * add a paiment
     *
     * @Route("/add", name="billandgo_paiment_add")
     * @param         Request $req
     * @return        \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        $paiment = new Paiment();
        $form = $this->createForm(PaimentType::class, $paiment, array('uid' => $user->getId()));
        if ($req->isMethod('POST')) {
            if (($form->handleRequest($req)->isValid())) {
                $paiment->setRefUser($user);
                if (($paiment->getRefBill()[0]->getRefUser() == $user) && !($paiment->getRefBill()[0]->getType())) {
                    $doc = $paiment->getRefBill()[0];
                    $doc->addRefPaiment($paiment);
                    $doc->setStatus("partially");
                    if ($doc->getSumPaiments() >= ($doc->getHT() + $doc->getVAT())) {
                        $doc->setStatus("paid");
                    }
                    $manager->persist($paiment);
                    $manager->flush();
                    return $this->redirect($this->generateUrl("billandgo_paiment_show", array('id' => $paiment->getId())));
                }
            }
        }
        return $this->render(
            'BillAndGoBundle:Paiment:add.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
            )
        );
    }

    /**
     * add a paiment
     *
     * @Route("/add/{id}", name="billandgo_paiment_add_from_bill")
     * @param              Request $req
     * @param              int     $id
     * @return             \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addFromBillAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
        if ($req->isMethod('POST') && ($document->getRefUser() == $user) && !($document->getType())) {
            $paiment = new Paiment();
            $paiment->setRefUser($user);
            $paiment->setRefBill($document);
            $document->addRefPaiment($paiment);
            $document->setStatus("partially");
            if (($document->getSumPaiments() + $req->request->get('paimentAmount')) >= ($document->getHT() + $document->getVAT())) {
                $document->setStatus("paid");
            }
            $paiment->setAmount($req->request->get('paimentAmount'));
            $paiment->setMode($req->request->get('paimentMode'));
            $paiment->setDatePaiment(new \DateTime($req->request->get('paimentDate')));
            $manager->persist($paiment);
            $manager->flush();
        }
        return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $document->getId())));
    }

    /**
     * Finds and displays a paiment entity.
     *
     * @Route("/{id}", name="billandgo_paiment_show")
     * @Method("GET")
     */
    public function showAction(Paiment $paiment)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($paiment->getRefUser() != $user) {
            $ar401 = ["wrong user"];
            return new Response(json_encode($ar401), 401);
        }
        $deleteForm = $this->createDeleteForm($paiment);
        return $this->render(
            'BillAndGoBundle:Paiment:show.html.twig', array(
            'paiment' => $paiment,
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a paiment entity.
     *
     * @Route("/{id}",   name="paiment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Paiment $paiment)
    {
        $form = $this->createDeleteForm($paiment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($paiment);
            $em->flush();
        }

        return $this->redirectToRoute('billandgo_paiment_index');
    }

    /**
     * Creates a form to delete a paiment entity.
     *
     * @param Paiment $paiment The paiment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Paiment $paiment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('paiment_delete', array('id' => $paiment->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
