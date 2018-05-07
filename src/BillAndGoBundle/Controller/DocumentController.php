<?php

/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */


namespace BillAndGoBundle\Controller;

use BillAndGoBundle\Entity\Numerotation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BillAndGoBundle\Entity\User;
use BillAndGoBundle\Entity\Document;
use BillAndGoBundle\Entity\Line;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Form;
use BillAndGoBundle\Form\LineEstimateType;
use BillAndGoBundle\Form\LineBillType;
use BillAndGoBundle\Form\DocumentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class DocumentController extends Controller
{
    private $status = [
        "draw", "canceled", "refused",
        "estimated", "accepted",
        "billed", "partially", "paid"
    ];

    /**
     * Lists all estimates entities.
     *
     * @Route("/estimates", name="billandgo_estimate_index")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexEstimate()
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        $estimates = $manager->getRepository('BillAndGoBundle:Document')->findAllEstimate($user->getId());

        return $this->render('BillAndGoBundle:document:index.html.twig', array(
            'list' => $estimates,
            'user' => $user,
            'type' => 'estimate',
            'limitation' =>  $this->getLimitation("quote")
        ));
    }

    /**
     * Lists all bills entities.
     *
     * @Route("/bills", name="billandgo_bill_index")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexBill()
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        $bills = $manager->getRepository('BillAndGoBundle:Document')->findAllBill($user->getId());

        return $this->render('BillAndGoBundle:document:indexBill.html.twig', array(
            'list' => $bills,
            'user' => $user,
            'type' => 'bill',
            'limitation' =>  $this->getLimitation("bill")
        ));
    }

    /**
     * Finds and displays a document entity.
     *
     * @Route("/documents/{id}", name="billandgo_document_view")
     * @Method({"GET", "POST"})
     * @param Request $req post request of edit, split, line creation or line edition
     * @param int $id id of the project
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
            if ($document != NULL) {
                if ($document->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
                }
                $line = new Line();
                if ($document->getType())
                    $form = $this->get('form.factory')->create(LineEstimateType::class, $line);
                else
                    $form = $this->get('form.factory')->create(LineBillType::class, $line);
                if ($req->isMethod('POST')) {
                    $ret = $this->viewPostAction($id, $req, $user, $document, $form, $line);
                    if ($ret < 0){
                        $ar500 = ["wrong request : code ".$ret];
                        return new Response(json_encode($ar500), 500);
                    }
                    else
                        $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
                }
                $taxes = $manager->getRepository('BillAndGoBundle:Tax')->findAll($id);
                $names = $manager->getRepository('BillAndGoBundle:Line')->lastLinesNames($user);
                $descriptions = $manager->getRepository('BillAndGoBundle:Line')->lastLinesDescriptions($user);
                return $this->render('BillAndGoBundle:document:full.html.twig', array(
                    'document' => $document,
                    'taxes' => $taxes,
                    'names' => $names,
                    'descriptions' => $descriptions,
                    'form' => $form->createView(),
                    'user' => $user
                ));
            }
        }
        return $this->redirect($this->generateUrl("billandgo_estimate_index"));
    }

    /**
     * call by viewAction
     * edit, add or split lines in the document
     * or edit document
     *
     * @param int $id id of current project
     * @param Request $req request sent to viewAction
     * @param User $user current user
     * @param Document $document current document
     * @param Form $form form LineProjectType
     * @param Line $line new line if creation
     * @return int
     */
    private function viewPostAction(int $id, Request $req, User $user, Document $document, Form $form, Line $line)
    {
        $manager = $this->getDoctrine()->getManager();
        $split = $req->request->get('split');
        $id_line = $req->request->get('id_line');
        $edit['name'] = $req->request->get('name');
        $edit['number'] = $req->request->get('number');
        $edit['description'] = $req->request->get('description');
        $edit['quantity'] = $req->request->get('quantity');
        $edit['price'] = $req->request->get('price');
        $edit['refTax'] = $req->request->get('refTax');
        $edit['estim'] = $req->request->get('estimated_time');
        $edit['chrono'] = $req->request->get('chrono_time');
        $edit['deadline'] = $req->request->get('deadLine');

        //split line
        if (($split) && ($id_line)) {
            if ($this->splitLine($id, $id_line, $split, $user) != 0)
                return -500;
            else
                return 1;
        }

        //edit document
        elseif (($edit['number']) && ($edit['description']))
        {
            $document->setNumber($edit['number']);
            $document->setDescription($edit['description']);
            $manager->flush();
            return 2;
        }

        //edit line
        elseif (($id_line) && ($edit['name']) && ($edit['description'])
            && ($edit['quantity']) && ($edit['price'])
            && ($edit['deadline']))
        {
            $ret = $this->editLine($id_line, $edit, $user);
            if ($ret == 0)
                return 3;
            else
                return $ret;
        }
        //create line
        elseif ($form->handleRequest($req)->isValid())
        {
            $line->setRefUser($user);
            $line->setRefClient($document->getRefClient());
            $line->setStatus($document->getStatus());
            if ($document->getType())
                $document->addRefLine($line);
            else
                $document->addRefLinesB($line);
            $manager->persist($line);
            $manager->flush();
            return 4;
        }
        return 0;
    }

    /**
     * edit status and update sentDate, answerDate if neeeded
     * @Route("/documents/{id}/edit/status/{status}", name="billandgo_document_edit_status")
     * @Method("GET")
     * @param int $id of the document
     * @param string $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editStatus(int $id, string $status)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        if (($id > 0) && (in_array($status, $this->status))) {
            $manager = $this->getDoctrine()->getManager();
            $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
            if ($document != NULL) {
                if ($document->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
                }
                $old_status = $document->getStatus();
                if ($status == "canceled") {
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                }
                else if (($status == "draw") && ($old_status == "canceled")) {
                    $document->setSentDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                }
                else if (($status == "estimated") && ($old_status == "draw")) {
                    $document->setSentDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                }
                else if (($status == "accepted") && ($old_status == "estimated")) {
                    $document->setAnswerDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                }
                else if (($status == "refused") && ($old_status == "estimated")) {
                    $document->setAnswerDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                }
                else if (($status == "billed") && ($old_status == "draw")) {
                    $document->setSentDate(new \DateTime());
                    $sumPaiments = $document->getSumPaiments();
                    if ($sumPaiments >= ($document->getHT() + $document->getVAT())) {
                        $newStatus = "paid";
                    }
                    elseif ($sumPaiments > 0) {
                        $newStatus = "partially";
                    }
                    else {
                        $newStatus = "billed";
                    }
                    $document->setStatus($newStatus);
                    $this->linesStatus($document, $status);
                }
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $id)));
            }
        }
        $ar404 = ["document doesn't exist"];
        return new Response(json_encode($ar404), 404);
    }

    private function linesStatus ($document, $status) {
        $estim_status = ['canceled', 'draw', 'estimated', 'accepted', 'refused'];
        $manager = $this->getDoctrine()->getManager();
        $lines = $document->getRefLines();
        foreach ($lines as $line) {
            if (in_array($line->getStatus(), $estim_status)) {
                $line->setStatus($status);
            }
        }
        $manager->flush();
    }

    /**
     * split line : create another line et reduce quantity of the first one
     * called by viewPostAction
     *
     * @param int $id id of the document
     * @param int $id_line id of the line to split
     * @param int $split quantity to split : will be remove from the existing line and attributed to the new one
     * @param User $user current user
     * @return int
     */
    private function splitLine ($id, $id_line, $split, $user) {
        $manager = $this->getDoctrine()->getManager();
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
        $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
        if (($line != NULL) && ($line->getQuantity() > $split)) {
            if (($line->getRefUser() != $user) || ($document->getRefUser() != $user))
                return 401;
            $new_line = new Line();
            $new_line->setRefTax($line->getRefTax());
            $new_line->setStatus($line->getStatus());
            $new_line->setRefClient($line->getRefClient());
            $new_line->setPrice($line->getPrice());
            $new_line->setName($line->getName());
            $new_line->setEstimatedTime($line->getEstimatedTime());
            $new_line->setDescription($line->getDescription());
            $new_line->setDeadLine($line->getDeadLine());
            $new_line->setRefUser($user);
            $new_line->setChronoTime(0);
            $new_line->setQuantity($split);
            $line->setQuantity($line->getQuantity() - $split);
            if ($document->getType())
                $document->addRefLine($new_line);
            else
                $document->addRefLinesB($new_line);
            $manager->persist($new_line);
            $manager->flush();
            return 0;
        }
        return -1;
    }

    /**
     * edit line
     * called by ViewPostAction
     *
     * @param int $id_line id of the line to edit
     * @param array $edit array with all edit informatons : name, description, quantity, price, reftax, estim, chrono, deadline
     * @param User $user current user
     * @return int
     */
    private function editLine($id_line, $edit, $user) {
        $manager = $this->getDoctrine()->getManager();
        $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
        if ($line == NULL)
            return 404;
        if ($line->getRefUser() != $user)
            return 401;
        $line->setName($edit['name']);
        $line->setDescription($edit['description']);
        $line->setQuantity($edit['quantity']);
        $line->setPrice($edit['price']);
        if (isset($edit['refTax'])) {
            $new_tax = $manager->getRepository('BillAndGoBundle:Tax')->find($edit['refTax']);
            $line->setRefTax($new_tax);
        }
        if (isset($edit['estim']) && $edit['estim'])
            $line->setEstimatedTime($edit['estim']);
        if (isset($edit['chrono']) && $edit['chrono'])
            $line->setChronoTime($edit['chrono']);
        $line->setDeadline(new \DateTime($edit['deadline']));
        $manager->flush();
        return 0;
    }

    /**
     * add an estimate
     *
     * @Route("/estimates/add", name="billandgo_estimate_add")
     * @Method({"GET", "POST"})
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addEstimateAction(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }

        if (false === $this->getLimitation("quote")) {
            return $this->redirect($this->generateUrl("billandgo_limitation"));
        }
        $manager = $this->getDoctrine()->getManager();

        $numerotationArray = $manager->getRepository("BillAndGoBundle:Numerotation")->findByRefUser($user);
        if (!(isset($numerotationArray[0]))) {
            $num = new Numerotation();
            $num->setRefUser($user);
            $num->setBillIndex(0);
            $num->setEstimateIndex(1);
            $num->setBillYearMonth(date("Ym"));
            $num->setEstimateYearMonth(date("Ym"));
            $manager->persist($num);
        }
        else {
            $num = $numerotationArray[0];
            if ($num->getEstimateYearMonth() != date("Ym")) {
                $num->setEstimateYearMonth(date("Ym"));
                $num->setEstimateIndex(1);
            }
            else $num->setEstimateIndex($num->getEstimateIndex() + 1);
        }
        $index = $num->getEstimateIndex();

        $estimate = new Document();
        $form = $this->createForm(DocumentType::class, $estimate, array('uid' => $user->getId()));
        if ($req->isMethod('POST')) {
            if (($form->handleRequest($req)->isValid())) {
                $estimate->setNumber("DEV-" . date("Y-m-") . str_pad($index, 3, "0", STR_PAD_LEFT));
                $estimate->setRefUser($user);
                $estimate->setType(1);
                $estimate->setStatus("draw");
                $manager->persist($estimate);
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $estimate->getId())));
            }
        }
        return $this->render('BillAndGoBundle:document:addEstimate.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * add a bill
     *
     * @Route("/bills/add", name="billandgo_bill_add")
     * @Method({"GET", "POST"})
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addBillAction(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }

        if (false === $this->getLimitation("bill")) {
            return $this->redirect($this->generateUrl("billandgo_limitation"));
        }

        $manager = $this->getDoctrine()->getManager();

        $numerotationArray = $manager->getRepository("BillAndGoBundle:Numerotation")->findByRefUser($user);
        if (!(isset($numerotationArray[0]))) {
            $num = new Numerotation();
            $num->setRefUser($user);
            $num->setBillIndex(1);
            $num->setEstimateIndex(0);
            $num->setBillYearMonth(date("Ym"));
            $num->setEstimateYearMonth(date("Ym"));
            $manager->persist($num);
        }
        else {
            $num = $numerotationArray[0];
            if ($num->getBillYearMonth() != date("Ym")) {
                $num->setBillYearMonth(date("Ym"));
                $num->setBillIndex(1);
            }
            else $num->setBillIndex($num->getBillIndex() + 1);
        }
        $index = $num->getBillIndex();

        $bill = new Document();
        $form = $this->createForm(DocumentType::class, $bill, array('uid' => $user->getId()));
        if ($req->isMethod('POST')) {
            if (($form->handleRequest($req)->isValid())) {
                $bill->setNumber("FAC-" . date("Y-m-") . str_pad($index, 3, "0", STR_PAD_LEFT));
                $bill->setRefUser($user);
                $bill->setType(0);
                $bill->setStatus("draw");
                $manager->persist($bill);
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $bill->getId())));
            }
        }
        return $this->render('BillAndGoBundle:document:addBill.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Route("/bills/add/estimate/lines", name="billandgo_bill_create_from_lines")
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addBillFromEstimateLines (Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if (false === $this->getLimitation("bill")) {
            return $this->redirect($this->generateUrl("billandgo_limitation"));
        }

        $post = $req->request->all();
        $lines_id = array();
        foreach ($post as $post_id => $post_elt) {
            if ($post_id == "name")
                $name = $post_elt;
            elseif ($post_id == "description")
                $description = $post_elt;
            elseif ($post_id == "estimate")
                $estimate_id = $post_elt;
            elseif ($post_id == "deadline")
                $deadline = $post_elt;
            else
                $lines_id[] = substr($post_id, 12);
        }
        $manager = $this->getDoctrine()->getManager();

        $numerotationArray = $manager->getRepository("BillAndGoBundle:Numerotation")->findByRefUser($user);
        if (!(isset($numerotationArray[0]))) {
            $num = new Numerotation();
            $num->setRefUser($user);
            $num->setBillIndex(1);
            $num->setEstimateIndex(0);
            $num->setBillYearMonth(date("Ym"));
            $num->setEstimateYearMonth(date("Ym"));
            $manager->persist($num);
        }
        else {
            $num = $numerotationArray[0];
            if ($num->getBillYearMonth() != date("Ym")) {
                $num->setBillYearMonth(date("Ym"));
                $num->setBillIndex(1);
            }
            else $num->setBillIndex($num->getBillIndex() + 1);
        }
        $index = $num->getBillIndex();

        $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimate_id);
        if ($estimate == NULL)
            return $this->redirect($this->generateUrl("billandgo_bill_index"));
        if ($estimate->getRefUser() != $user)
        {
            $ar401 = ['wrong user'];
            return new Response(json_encode($ar401), 401);
        }
        $bill = new Document();
        $bill->setNumber("FAC-" . date("Y-m-") . str_pad($index, 3, "0", STR_PAD_LEFT));
        $bill->setDescription($description);
        $bill->setStatus("draw");
        $bill->setRefUser($user);
        $bill->setRefClient($estimate->getRefClient());
        $bill->setType(0);
        if (isset($deadline))
            $bill->setDelayDate(new \DateTime($deadline));
        foreach ($lines_id as $line_id) {
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($line_id);
            if ($line->getRefUser() != $user) {
                $ar401 = ['wrong user'];
                return new Response(json_encode($ar401), 401);
            }
            $bill->addRefLinesB($line);
            $line->addRefBill($bill);
            $manager->persist($line);
        }
        $bill->addRefEstimate($estimate);
        $manager->persist($bill);
        $manager->flush();
        return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $bill->getId())));
    }

    /**
     * create a facture form an estimate, refered by estimate_id
     *
     * @Route("bills/add/estimate/{estimate_id}", name="billandgo_bill_create_from_estimate")
     * @param Request $req post request from the facturation form
     * @param int $estimate_id estimate id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createFromEstimateAction(Request $req, int $estimate_id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }

        if ($req->isMethod('POST')) {
            $manager = $this->getDoctrine()->getManager();
            $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimate_id);
            if ($estimate == NULL) return $this->redirect($this->generateUrl("billandgo_bill_index"));
            if ($estimate->getRefUser() != $user)
            {
                $ar401 = ['wrong user'];
                return new Response(json_encode($ar401), 401);
            }

            $numerotationArray = $manager->getRepository("BillAndGoBundle:Numerotation")->findByRefUser($user);
            if (!(isset($numerotationArray[0]))) {
                $num = new Numerotation();
                $num->setRefUser($user);
                $num->setBillIndex(1);
                $num->setEstimateIndex(0);
                $num->setBillYearMonth(date("Ym"));
                $num->setEstimateYearMonth(date("Ym"));
                $manager->persist($num);
            }
            else {
                $num = $numerotationArray[0];
                if ($num->getBillYearMonth() != date("Ym")) {
                    $num->setBillYearMonth(date("Ym"));
                    $num->setBillIndex(1);
                }
                else $num->setBillIndex($num->getBillIndex() + 1);
            }
            $index = $num->getBillIndex();

            $bill = new Document();
            $bill->setRefUser($user);
            $bill->setRefClient($estimate->getRefClient());
            $bill->setNumber("FAC-" . date("Y-m-") . str_pad($index, 3, "0", STR_PAD_LEFT));
            $bill->setDelayDate(new \DateTime($req->get("delay")));
            $bill->setType(0);
            $bill->setDescription($estimate->getDescription());
            $bill->setStatus("draw");
            foreach ($estimate->getRefLines() as $line) {
                if ($line->getRefBill()->isEmpty()) {
                    $bill->addRefLinesB($line);
                    $line->addRefBill($bill);
                }
            }
            $manager->persist($bill);
            $manager->flush();
            return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $bill->getId())));
        }
        return $this->redirect($this->generateUrl("billandgo_bill_index"));
    }

    /**
     * @Route("bills/add/project/{project_id}", name="billandgo_bill_create_from_project")
     * @param Request $req, int $project_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createFromProjectAction(Request $req, int $project_id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }

        if ($req->isMethod('POST')) {
            $manager = $this->getDoctrine()->getManager();
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($project_id);
            if ($project == NULL) return $this->redirect($this->generateUrl("billandgo_bill_index"));
            if ($project->getRefUser() != $user)
            {
                $ar401 = ['wrong user'];
                return new Response(json_encode($ar401), 401);
            }

            $numerotationArray = $manager->getRepository("BillAndGoBundle:Numerotation")->findByRefUser($user);
            if (!(isset($numerotationArray[0]))) {
                $num = new Numerotation();
                $num->setRefUser($user);
                $num->setBillIndex(1);
                $num->setEstimateIndex(0);
                $num->setBillYearMonth(date("Ym"));
                $num->setEstimateYearMonth(date("Ym"));
                $manager->persist($num);
            }
            else {
                $num = $numerotationArray[0];
                if ($num->getBillYearMonth() != date("Ym")) {
                    $num->setBillYearMonth(date("Ym"));
                    $num->setBillIndex(1);
                }
                else $num->setBillIndex($num->getBillIndex() + 1);
            }
            $index = $num->getBillIndex();

            $bill = new Document();
            $bill->setRefUser($user);
            $bill->setRefClient($project->getRefClient());
            $bill->setNumber("FAC-" . date("Y-m-") . str_pad($index, 3, "0", STR_PAD_LEFT));
            $bill->setType(0);
            $bill->setDescription($project->getDescription());
            $bill->setStatus("draw");
            foreach ($project->getRefLines() as $line) {
                if ($line->getRefBill()->isEmpty()) {
                    $bill->addRefLinesB($line);
                    $line->addRefBill($bill);
                }
            }
            $manager->persist($bill);
            $manager->flush();
            return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $bill->getId())));
        }
        return $this->redirect($this->generateUrl("billandgo_project_list"));
    }

    /**
     * @Route("documents/{doc_id}/send", name="billandgo_document_send_email")
     * @Method("GET")
     * @param int $doc_id
     * @param Request $req get request containing client_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function sendEmail (int $doc_id, Request $req)
    {
        //check user
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }

        $manager = $this->getDoctrine()->getManager();
        $contact_id = $req->query->get("contact");
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($doc_id);
        $contact = $manager->getRepository("BillAndGoBundle:ClientContact")->find($contact_id);
        if ($document == NULL) return $this->redirect($this->generateUrl("billandgo_bill_index"));
        if ($contact == NULL) return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $doc_id)));
        if (($document->getRefUser() != $user) || ($document->getRefUser() != $user))
        {
            $ar401 = ['wrong user'];
            return new Response(json_encode($ar401), 401);
        }
        if ($document->getType()) {
            $type = "estimate";
            $rand = random_int(1, 1000000000);
        }
        else {
            $type = "bill";
            $rand = 0;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject(ucfirst($type)." : ".$document->getNumber()." de ".$user->getCompanyName())
            ->setFrom(array('billandgo@iumio.com' => "Bill&Go Service"))
            ->setTo($contact->getEmail())
            ->setBody(
                $this->renderView(
                    "BillAndGoBundle:document:mailEstimate.html.twig",
                    array(
                        "type" => $type,
                        "document" => $document,
                        "user" => $user,
                        "rand" => $rand,
                        "contact" => $contact
                    )
                ),
                "text/html"
            )
            ;
        $this->get("mailer")->send($message);
        $document->setToken($rand);
        $manager->flush();
        /*return $this->render("BillAndGoBundle:document:mailEstimate.html.twig",
            array(
                "type" => $type,
                "document" => $document,
                "user" => $user,
                "rand" => $rand
            ));*/
        if ($document->getType())
            return $this->redirectToRoute("billandgo_document_edit_status", array("id" => $doc_id, "status" => "estimated"));
        else
            return $this->redirectToRoute("billandgo_document_edit_status", array("id" => $doc_id, "status" => "billed"));
        return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $doc_id)));
    }

    /**
     * @Route("documents/{doc_id}/return/{token}/{answer}", name="billandgo_document_email_accept")
     * @Method("GET")
     * @param int $doc_id
     * @param int $token
     * @param int $answer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function answerEmail (int $doc_id, int $token, int $answer)
    {
        $manager = $this->getDoctrine()->getManager();
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($doc_id);
        if ($document == NULL) return new Response("404");
        if ($document->getToken() == $token) {
            if (($answer == 1) && ($document->getStatus() == "estimated")) {
                $document->setAnswerDate(new \DateTime());
                $document->setStatus("accepted");
                $this->linesStatus($document, "accepted");
                return $this->render("BillAndGoBundle:document:accepted.html.twig",
                    array(
                        "type" => "devis",
                    ));
            }
            else if (($answer == 0) && ($document->getStatus() == "estimated")) {
                $document->setAnswerDate(new \DateTime());
                $document->setStatus("refused");
                $this->linesStatus($document, "refused");
                return new Response("dommage ! :'(");
            }
        }
        return new Response("401");
    }

    public function getLimitation($type) {
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
                case 'project' :
                    if (count($projects) >= 15) {
                        return (false);
                    }
                    return (true);
                    break;
                case 'bill' :
                    if (count($bills) >= 15) {
                        return (false);
                    }
                    return (true);
                    break;
                case 'quote' :
                    if (count($quotes) >= 15) {
                        return (false);
                    }
                    return (true);
                    break;
                case 'client' :
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
