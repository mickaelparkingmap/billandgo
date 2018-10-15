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
use AppBundle\Service\DocumentService;
use AppBundle\Service\SuggestionService;
use BillAndGoBundle\Entity\Client;
use BillAndGoBundle\Entity\Numerotation;
use BillAndGoBundle\Repository\NumerotationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BillAndGoBundle\Entity\User;
use BillAndGoBundle\Entity\Document;
use BillAndGoBundle\Entity\Line;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Form;
use BillAndGoBundle\Form\LineEstimateType;
use BillAndGoBundle\Form\LineBillType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DocumentController
 * @package BillAndGoBundle\Controller
 */
class DocumentController extends Controller
{
    /** @var array  */
    private $status = [
        "draw", "canceled", "refused",
        "estimated", "accepted",
        "billed", "partially", "paid"
    ];
    /** @var DocumentService $documentService */
    private $documentService;
    /** @var ClientService $clientService */
    private $clientService;
    /** @var SuggestionService $suggestionService */
    private $suggestionService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->documentService = $this->get("AppBundle\Service\DocumentService");
        $this->clientService = $this->get("AppBundle\Service\ClientService");
        $this->suggestionService = $this->get("billandgo.suggestion");
    }


    /**
     * Lists all estimates entities.
     *
     * @Route ("/estimates", name="billandgo_estimate_index")
     * @Method("GET")
     * @return Response
     */
    public function indexEstimate(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $begin = $request->get('begin');
        $end = $request->get('end');
        $estimates = $this->documentService->listDrawFromUser($user, $begin, $end);
        $manager = $this->getDoctrine()->getManager();
        $sync = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "sync_task_calendar"));
        return $this->render(
            'BillAndGoBundle:document:index.html.twig',
            array(
                'list' => $estimates,
                'user' => $user,
                'type' => 'estimate',
                'syncTask' => (null === $sync)? "inactive" : $sync->getValue(),
                "usersub" => DefaultController::userSubscription($user, $this)
            )
        );
    }

    /**
     * Lists all bills entities.
     *
     * @Route("/bills", name="billandgo_bill_index")
     * @Method("GET")
     * @return Response
     */
    public function indexBill(Request $request) : Response
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $begin = $request->get('begin');
        $end = $request->get('end');
        $bills = $this->documentService->listBillsFromUser($user, $begin, $end);

        return $this->render(
            'BillAndGoBundle:document:indexBill.html.twig',
            array(
                'list' => $bills,
                'user' => $user,
                'type' => 'bill',
                "usersub" => DefaultController::userSubscription($user, $this)
            )
        );
    }

    /**
     * Finds and displays a document entity.
     *
     * @Route("/documents/{id}", name="billandgo_document_view")
     * @Method({"GET",           "POST"})
     * @param                    Request $req post request of edit, split, line creation or line edition
     * @param                    int     $id  id of the project
     * @return                   \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $req, int $id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return new Response(json_encode($usersub["msg"]), 401);
        }
        if ($id > 0) {
            $document = $this->documentService->getDocument($user, $id);
            if ($document != null) {
                $manager = $this->getDoctrine()->getManager();
                if ($document->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
                }
                $line = new Line();
                if ($document->isEstimate()) {
                    $form = $this->get('form.factory')->create(LineEstimateType::class, $line);
                } else {
                    $form = $this->get('form.factory')->create(LineBillType::class, $line);
                }
                if ($req->isMethod('POST')) {
                    $ret = $this->viewPostAction($id, $req, $user, $document, $form, $line);
                    if ($ret < 0) {
                        $ar500 = ["wrong request : code ".$ret];
                        return new Response(json_encode($ar500), 500);
                    } else {
                        $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
                    }
                }
                $taxes = $manager->getRepository('BillAndGoBundle:Tax')->findAll();
                $suggestions = $this->suggestionService->getList($user);
                $sync = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
                    array("user" => $user->getId(), "name" => "sync_task_calendar"));
                return $this->render(
                    'BillAndGoBundle:document:full.html.twig',
                    array(
                        'document'      => $document,
                        'taxes'         => $taxes,
                        'suggestions'   => $suggestions,
                        'form'          => $form->createView(),
                        'user'          => $user,
                        "usersub" => DefaultController::userSubscription($user, $this),
                        'syncTask' => (null === $sync)? "inactive" : $sync->getValue()
                    )
                );
            }
        }
        return $this->redirect($this->generateUrl("billandgo_estimate_index"));
    }

    /**
     * call by viewAction
     * edit, add or split lines in the document
     * or edit document
     *
     * @param  int      $id       id of current project
     * @param  Request  $req      request sent to viewAction
     * @param  User     $user     current user
     * @param  Document $document current document
     * @param  Form     $form     form LineProjectType
     * @param  Line     $line     new line if creation
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
            if ($this->splitLine($id, $id_line, $split, $user) != 0) {
                return -500;
            } else {
                return 1;
            }
        } //edit document
        elseif (($edit['number']) && ($edit['description'])) {
            $document->setNumber($edit['number']);
            $document->setDescription($edit['description']);
            $manager->flush();
            return 2;
        } //edit line
        elseif (($id_line) && ($edit['name']) && ($edit['description'])
            && ($edit['quantity']) && ($edit['price'])
            && ($edit['deadline'])
        ) {
            $ret = $this->editLine($id_line, $edit, $user);
            if ($ret == 0) {
                return 3;
            } else {
                return $ret;
            }
        } //create line
        elseif ($form->handleRequest($req)->isValid()) {
            $line->setRefUser($user);
            $line->setRefClient($document->getRefClient());
            $line->setStatus($document->getStatus());
            if ($document->isEstimate()) {
                $document->addRefLine($line);
            } else {
                $document->addRefLinesB($line);
            }
            $lineData = $req->request->get('billandgobundle_line');
            $suggestion = $this->suggestionService->update(
                $user,
                $lineData['name'],
                $lineData['description'],
                $lineData['price'],
                $lineData['estimatedTime'] ?? null
            );
            $manager->persist($suggestion);
            $manager->persist($line);
            $manager->flush();
            return 4;
        }
        return 0;
    }

    /**
     * edit status and update sentDate, answerDate if neeeded
     *
     * @Route("/documents/{id}/edit/status/{status}", name="billandgo_document_edit_status")
     * @Method("GET")
     * @param                                         int    $id     of the document
     * @param                                         string $status
     * @return                                        \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editStatus(int $id, string $status)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }

        $st = $this->editDocStatus($id, $status);
        if (2 === $st) {
            $ar401 = ["wrong user"];
            return new Response(json_encode($ar401), 401);
        }
        elseif (1 === $st) {
            return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $id)));
        }
        else {
            $ar404 = ["document doesn't exist"];
            return new Response(json_encode($ar404), 404);
        }
    }

    public function editDocStatus(int $id, string $status)
    {
        $user = $this->getUser();
        if (($id > 0) && (in_array($status, $this->status))) {
            $manager = $this->getDoctrine()->getManager();
            $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
            if ($document != null) {
                if ($document->getRefUser() != $user) {
                    return (2);
                }
                $old_status = $document->getStatus();
                if ($status == "canceled") {
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                } elseif (($status == "draw") && ($old_status == "canceled")) {
                    $document->setSentDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                } elseif (($status == "estimated") && ($old_status == "draw")) {
                    $document->setSentDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                } elseif (($status == "accepted") && ($old_status == "estimated")) {
                    $document->setAnswerDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                } elseif (($status == "refused") && ($old_status == "estimated")) {
                    $document->setAnswerDate(new \DateTime());
                    $document->setStatus($status);
                    $this->linesStatus($document, $status);
                } elseif (($status == "billed") && ($old_status == "draw")) {
                    $document->setSentDate(new \DateTime());
                    $sumPaiments = $document->getSumPaiments();
                    if ($sumPaiments >= ($document->getHT() + $document->getVAT())) {
                        $newStatus = "paid";
                    } elseif ($sumPaiments > 0) {
                        $newStatus = "partially";
                    } else {
                        $newStatus = "billed";
                    }
                    $document->setStatus($newStatus);
                    $this->linesStatus($document, $status);
                }
                $manager->flush();
               return (1);
            }
        }
       return (0);
    }

    private function linesStatus($document, $status)
    {
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
     * @param  int  $id      id of the document
     * @param  int  $id_line id of the line to split
     * @param  int  $split   quantity to split : will be remove from the existing line and attributed to the new one
     * @param  User $user    current user
     * @return int
     */
    private function splitLine($id, $id_line, $split, $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
        $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
        if (($line != null) && ($line->getQuantity() > $split)) {
            if (($line->getRefUser() != $user) || ($document->getRefUser() != $user)) {
                return 401;
            }
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
            if ($document->isEstimate()) {
                $document->addRefLine($new_line);
            } else {
                $document->addRefLinesB($new_line);
            }
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
     * @param  int   $id_line id of the line to edit
     * @param  array $edit    array with all edit informatons : name, description, quantity, price, reftax, estim, chrono, deadline
     * @param  User  $user    current user
     * @return int
     */
    private function editLine($id_line, $edit, $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
        if ($line == null) {
            return 404;
        }
        if ($line->getRefUser() != $user) {
            return 401;
        }
        $line->setName($edit['name']);
        $line->setDescription($edit['description']);
        $line->setQuantity($edit['quantity']);
        $line->setPrice($edit['price']);
        if (isset($edit['refTax'])) {
            $new_tax = $manager->getRepository('BillAndGoBundle:Tax')->find($edit['refTax']);
            $line->setRefTax($new_tax);
        }
        if (isset($edit['estim']) && $edit['estim']) {
            $line->setEstimatedTime($edit['estim']);
        }
        if (isset($edit['chrono']) && $edit['chrono']) {
            $line->setChronoTime($edit['chrono']);
        }
        $line->setDeadline(new \DateTime($edit['deadline']));
        $manager->flush();
        return 0;
    }

    /**
     * @Route("/document/add/{step}", name="billandgo_document_add")
     * @Method({"GET", "POST"})
     * @param Request $req
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function addDocumentAction(Request $req, int $step) : Response
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            return new Response(json_encode(["disconnected"]), 401);
        }
        $type = $req->get('type');
        $clientID = (int) $req->get('client');
        $description = $req->get('description');
        $docID = (int) $req->get('doc');
        $delayDate = new \DateTime($req->get('delayDate'));
        if ((1 == $step) && (null !== $type) && (in_array($type, ['bill', 'estimate']))) {
            return $this->addDocumentStep1($user, $type);
        } elseif ((2 == $step) && (is_int($clientID)) && (in_array($type, ['bill', 'estimate']))) {
            return $this->addDocumentStep2($user, $type, $clientID);
        } elseif ((3 == $step) && (null !== $description) && (is_int($docID))) {
            return $this->addDocumentStep3($user, $docID, $description);
        } elseif ((4 == $step) && (is_int($docID)) && (null !== $delayDate)) {
            return $this->addDocumentStep4($user, $docID, $delayDate);
        }
        return $this->redirect($this->generateUrl('billandgo_dashboard'));
    }

    /**
     * @param User $user
     * @param string $type
     * @return Response
     */
    private function addDocumentStep1(User $user, string $type): Response
    {
        $clients = $this->clientService->getClientListFromUser($user);
        return $this->render(
            'BillAndGoBundle:document:addDocument.html.twig',
            array(
                'step'      => 2,
                'type'      => $type,
                'clients'   => $clients,
                'user'      => $user,
                "usersub" => DefaultController::userSubscription($user, $this)
            )
        );
    }

    /**
     * @param User $user
     * @param string $type
     * @param int $clientID
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function addDocumentStep2(User $user, string $type, int $clientID): Response
    {
        $client = $this->clientService->getClient($user, $clientID);
        if ($client instanceof Client) {
            $doc = $this->documentService->documentCreation($user, $type, $client);
            return $this->render(
                'BillAndGoBundle:document:addDocument.html.twig',
                array(
                    'step'      => 3,
                    'type'      => $type,
                    'doc'       => $doc,
                    'user'      => $user,
                    "usersub" => DefaultController::userSubscription($user, $this)
                )
            );
        }
        return $this->redirect($this->generateUrl('billandgo_dashboard'));
    }

    /**
     * @param User $user
     * @param int $docID
     * @param string $description
     * @return Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function addDocumentStep3(User $user, int $docID, string $description): Response
    {
        $doc = $this->documentService->setDescription($user, $description, $docID);
        return $this->render(
            'BillAndGoBundle:document:addDocument.html.twig',
            array(
                'step'      => 4,
                'type'      => $doc->isEstimate(),
                'doc'       => $doc,
                'user'      => $user,
                "usersub" => DefaultController::userSubscription($user, $this)
            )
        );
    }

    /**
     * @param User $user
     * @param int $docID
     * @param \DateTime $delayDate
     * @return Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function addDocumentStep4(User $user, int $docID, \DateTime $delayDate): Response
    {
        $doc = $this->documentService->setDelayDate($user, $delayDate, $docID);
        return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $doc->getId())));
    }

    /**
     * @Route("/bills/add/estimate/lines", name="billandgo_bill_create_from_lines")
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addBillFromEstimateLines(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }

        $post = $req->request->all();
        $lines_id = array();
        foreach ($post as $post_id => $post_elt) {
            if ($post_id == "name") {
                $name = $post_elt;
            } elseif ($post_id == "description") {
                $description = $post_elt;
            } elseif ($post_id == "estimate") {
                $estimate_id = $post_elt;
            } elseif ($post_id == "deadline") {
                $deadline = $post_elt;
            } else {
                $lines_id[] = substr($post_id, 12);
            }
        }
        $manager = $this->getDoctrine()->getManager();

        /** @var NumerotationRepository $numerotationRepo */
        $numerotationRepo = $manager->getRepository(Numerotation::class);
        $numerotationArray = $numerotationRepo->findByRefUser($user);
        if (!(isset($numerotationArray[0]))) {
            $num = new Numerotation();
            $num->setRefUser($user);
            $num->setBillIndex(1);
            $num->setEstimateIndex(0);
            $num->setBillYearMonth(date("Ym"));
            $num->setEstimateYearMonth(date("Ym"));
            $manager->persist($num);
        } else {
            /** @var Numerotation $num */
            $num = $numerotationArray[0];
            if ($num->getBillYearMonth() != date("Ym")) {
                $num->setBillYearMonth(date("Ym"));
                $num->setBillIndex(1);
            } else {
                $num->setBillIndex($num->getBillIndex() + 1);
            }
        }
        $index = $num->getBillIndex();

        $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimate_id);
        if ($estimate == null) {
            return $this->redirect($this->generateUrl("billandgo_bill_index"));
        }
        if ($estimate->getRefUser() != $user) {
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
        if (isset($deadline)) {
            $bill->setDelayDate(new \DateTime($deadline));
        }
        foreach ($lines_id as $line_id) {
            /** @var Line $line */
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
     * @param                                     Request $req         post request from the facturation form
     * @param                                     int     $estimate_id estimate id
     * @return                                    \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createFromEstimateAction(Request $req, int $estimate_id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }

        if ($req->isMethod('POST')) {
            $manager = $this->getDoctrine()->getManager();
            $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimate_id);
            if ($estimate == null) {
                return $this->redirect($this->generateUrl("billandgo_bill_index"));
            }
            if ($estimate->getRefUser() != $user) {
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
            } else {
                /** @var Numerotation $num */
                $num = $numerotationArray[0];
                if ($num->getBillYearMonth() != date("Ym")) {
                    $num->setBillYearMonth(date("Ym"));
                    $num->setBillIndex(1);
                } else {
                    $num->setBillIndex($num->getBillIndex() + 1);
                }
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
                /** @var Line $line */
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
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        if ($req->isMethod('POST')) {
            $manager = $this->getDoctrine()->getManager();
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($project_id);
            if ($project == null) {
                return $this->redirect($this->generateUrl("billandgo_bill_index"));
            }
            if ($project->getRefUser() != $user) {
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
            } else {
                /** @var Numerotation $num */
                $num = $numerotationArray[0];
                if ($num->getBillYearMonth() != date("Ym")) {
                    $num->setBillYearMonth(date("Ym"));
                    $num->setBillIndex(1);
                } else {
                    $num->setBillIndex($num->getBillIndex() + 1);
                }
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
                /** @var Line $line */
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
     * @Method("POST")
     * @param int     $doc_id
     * @param Request $req    get request containing client_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function sendEmail(int $doc_id, Request $req)
    {
        //check user
        $user = $this->getUser();
        if (!is_object($user)) {
            return new JsonResponse(["code" => "401", "msg" => "Veuillez vous connecter", 401]);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }

        $manager = $this->getDoctrine()->getManager();
        $contact_id = $req->get("contact");
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($doc_id);
        $contact = $manager->getRepository("BillAndGoBundle:ClientContact")->find($contact_id);
        if ($document == null) {
            return $this->redirect($this->generateUrl("billandgo_bill_index"));
        }
        if ($contact == null) {
            return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $doc_id)));
        }
        if (($document->getRefUser() != $user) || ($document->getRefUser() != $user)) {
            return new JsonResponse(["code" => "404", "msg" => "Document inexistant", 404]);
        }

        $readableType = "";
        if ($document->isEstimate()) {
            $type = "estimate";
            $readableType = "Devis";
            $rand = random_int(1, 1000000000);
        } else {
            $readableType = "Facture";
            $type = "bill";
            $rand = 0;
        }

        $sender = array('noreply@billandgo.fr' => "Bill&Go Service");

        $message = ((new \Swift_Message(($readableType." : ".$document->getNumber()." de ".$user->getCompanyName()))))
            ->setFrom($sender)
            ->setTo($contact->getEmail())
            ->setBody(
                $this->renderView(
                    "BillAndGoBundle:document:mailEstimate.html.twig",
                    array(
                        "type" => $type,
                        "document" => $document,
                        "user" => $user,
                        "rand" => $rand,
                        "contact" => $contact,
                        "usersub" => $usersub
                    )
                ),
                "text/html"
            );
        $mailer = $this->get("mailer");
        $mailer->send($message);
        $document->setToken($rand);
        $manager->flush();

        $this->editDocStatus($doc_id, (($document->isEstimate())? "estimated": "billed"));
        return new JsonResponse(["code" => 200, "msg" => "Le document a bien été envoyé"], 200);
    }

    /**
     * @Route("documents/{doc_id}/return/{token}/{answer}", name="billandgo_document_email_accept")
     * @Method("GET")
     * @param int $doc_id
     * @param int $token
     * @param int $answer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function answerEmail(int $doc_id, int $token, int $answer)
    {
        $manager = $this->getDoctrine()->getManager();
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($doc_id);
        if ($document == null) {
           throw new NotFoundHttpException("Content not found");
        }
        if ($document->getToken() == $token) {
            if (($answer == 1) && ($document->getStatus() == "estimated")) {
                $document->setAnswerDate(new \DateTime());
                $document->setStatus("accepted");
                $this->linesStatus($document, "accepted");
                return $this->render(
                    "BillAndGoBundle:document:accepted.html.twig",
                    array(
                        "type" => "devis",
                    )
                );
            } elseif (($answer == 0) && ($document->getStatus() == "estimated")) {
                $document->setAnswerDate(new \DateTime());
                $document->setStatus("refused");
                $this->linesStatus($document, "refused");
                return new Response("dommage ! :'(");
            }
        }
        throw new NotFoundHttpException("Content not found");
    }


    /**
     *
     * @Route("documents/{doc_id}/edit/save", name="billandgo_document_edit_save")
     * @Method("POST")
     * @param int $doc_id
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editDesc(int $doc_id, Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }

        $desc = $request->get("desc");

        if (null != $desc) {
            $manager = $this->getDoctrine()->getManager();
            $document = $this->getDoctrine()->getRepository('BillAndGoBundle:Document')->
            findOneBy(["id" => $doc_id, "refUser" => $user->getId()]);
            if ($document == null) {
               throw new NotFoundHttpException("Document non trouvée");
            }

            //$document = new Document();
            $document->setDescription($desc);
            $manager->merge($document);
            $manager->flush();
            return new JsonResponse(["OK"]);
        }
        throw new NotFoundHttpException("Valeur Description nulle");
    }




    /**
     * @Route("document/{id}/generate", name="billandgo_document_generate")
     * @Method("GET")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws
     */
    public function generateDocumentAction(int $id)
    {
        return $this->generateDocument($id);
    }

    /**
     * @Route("document/{id}/generate/public/{token}", name="billandgo_document_generate_public")
     * @Method("GET")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws
     */
    public function generatePublicDocumentAction(int $id, int $token)
    {
        return $this->generateDocument($id, true, $token);
    }


    /** Generate document PDF
     * @param int $id Document id
     * @param bool $public If public generation
     * @param int|null $token Document token
     * @return Response
     * @throws \Mpdf\MpdfException
     */
    public function generateDocument(int $id, bool $public = false, int $token = null)
    {
        $manager = $this->getDoctrine()->getManager();
        $assetsManager = new Package(new EmptyVersionStrategy());
        if (false === $public) {
            $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
            $user = $this->getUser();
            if (!is_object($user)) {
                $ar401 = ["not connected"];
                return new Response(json_encode($ar401), 401);
            }
        }
        else {
            $document = $manager->getRepository('BillAndGoBundle:Document')->findOneBy(
                array("id" => $id, "token" => $token));

            if (null === $document) {
                throw new NotFoundHttpException("Vous n'avez pas accès à ce document");
            }
            else {
                $user = $document->getRefUser();
            }
        }

        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        if ($document != null) {
            $manager = $this->getDoctrine()->getManager();
            if ($document->getRefUser() != $user) {
                $ar401 = ["wrong user"];
                return new Response(json_encode($ar401), 401);
            }
            $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
        }

        $selectedTemplate = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_choice"));

        $selectedTemplateCustom = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom"));

        $selfName = $user->getCompanyname();
        $selfAdress = $user->getAddress();
        $selfZipCode = $user->getZipCode();
        $selfSiret = $user->getSiret();

        $selfCity = $user->getCity();
        $selfCountry = $user->getCountry();

        $selfEmail = $user->getEmail();
        $selfTel = $user->getMobile();

        $clientName = $document->getRefClient()->getCompanyName();
        $clientAdress = $document->getRefClient()->getAdress();
        $clientZipCode =   $document->getRefClient()->getZipcode();
        $clientCity =  $document->getRefClient()->getCity();
        $clientCountry =  $document->getRefClient()->getCountry();

        $docNumber = $document->getNumber();
        $docType = ($document->isEstimate())? "DEVIS" : "FACTURE";

        $date_time =
            (null != $document->getSentDate() ) ? $document->getSentDate() : (new \DateTime())->format("Y-m-d");
        $intl_date_formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
        $date =  $intl_date_formatter->format($date_time);

        $totalHT = 0;
        $tax = "";
        $taxtotal = 0;

        $lines = ($document->isEstimate())? $document->getRefLines() : $document->getRefLinesB();
        foreach ($lines as $one) {
            $totalHT += $one->getQuantity()*$one->getPrice();
            $taxtotal += ($one->getRefTax()->getPercent() * ($one->getQuantity()*$one->getPrice())) / 100;
            $tax =  $one->getRefTax()->getName();
        }

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 40,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        if (null === $selectedTemplateCustom) {
            $content = $this->renderView("BillAndGoBundle:document:pdf/".((null === $selectedTemplate)?
                    "pdf.document.type.1":$selectedTemplate->getValue()).".html.twig",
                array(
                    "document" => $document,
                    "user" => $user,
                    "selfPremium" => ("paid" == $usersub["plan"]) ? true : false,
                    "sentDate" => $date,
                    "lines" => $lines,
                    "totalHT" => $totalHT,
                    "taxtotal" => $taxtotal,
                    "tax" => $tax,
                    "selfCompanyName" => $selfName,
                    "clientName" => $clientName,
                    "clientAddress" => $clientAdress,
                    "clientZipCode" => $clientZipCode,
                    "clientCity" => $clientCity,
                    "clientCountry" => $clientCountry,
                    "selfCompanyAdress" => $selfAdress,
                    "selfCompanyZipCode" => $selfZipCode,
                    "selfCompanyCity" => $selfCity,
                    "selfCompanyCountry" => $selfCountry,
                    "selfCompanyEmail" => $selfEmail,
                    "selfSiret" => $selfSiret,
                    "selfCompanyTel" => $selfTel,
                    "docNumber" => $docNumber,
                    "docType" => $docType,
                    "selfCompanyLogo" =>
                        $assetsManager->getUrl('uploads/user/company/'.$user->getCompanyLogoPath())

                ));
        }
        else {

            $document->setRefUser(null);
            $manager = $this->getDoctrine()->getManager();
            $selectedTemplateCustomStyle = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
                array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_style"));

            $selectedTemplateCustomHeader = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
                array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_header"));

            $selectedTemplateCustomBody = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
                array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_body"));

            $selectedTemplateCustomFooter = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
                array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_footer"));


            if (null === $user) {
                throw new NotFoundHttpException("Document non trouvé");
            }


            $global =   array(
                "document" => $document,
                "selfFirstname" => $user->getFirstname(),
                "selfLastname" => $user->getLastname(),
                "selfEmail" => $user->getEmail(),
                "selfBanque" => $user->getBanque(),
                "selfBic" => $user->getBic(),
                "selfIban" => $user->getIban(),
                "selfPremium" => ("paid" == $usersub["plan"]) ? true : false,
                "sentDate" => $date,
                "lines" => $lines,
                "totalHT" => $totalHT,
                "taxtotal" => $taxtotal,
                "tax" => $tax,
                "selfCompanyName" => $selfName,
                "clientName" => $clientName,
                "clientAddress" => $clientAdress,
                "clientZipCode" => $clientZipCode,
                "clientCity" => $clientCity,
                "clientCountry" => $clientCountry,
                "selfCompanyAdress" => $selfAdress,
                "selfCompanyZipCode" => $selfZipCode,
                "selfCompanyCity" => $selfCity,
                "selfCompanyCountry" => $selfCountry,
                "selfCompanyEmail" => $selfEmail,
                "selfSiret" => $selfSiret,
                "selfCompanyTel" => $selfTel,
                "docNumber" => $docNumber,
                "docType" => $docType,
                "selfCompanyLogo" =>
                    $assetsManager->getUrl('uploads/user/company/'.$user->getCompanyLogoPath())

            );

            $content = $this->renderView("BillAndGoBundle:document:base/pdf/pdf.document.type.0.html.twig",
                array(
                    'footer' => null === $selectedTemplateCustomFooter? "" : $selectedTemplateCustomFooter->getValue(),
                    'body' => null === $selectedTemplateCustomBody? "" : $selectedTemplateCustomBody->getValue(),
                    'header' => null === $selectedTemplateCustomHeader? "" : $selectedTemplateCustomHeader->getValue(),
                    'style' => null === $selectedTemplateCustomStyle? "" : $selectedTemplateCustomStyle->getValue(),
                    "document" => $document,
                    "selfFirstname" => $user->getFirstname(),
                    "selfLastname" => $user->getLastname(),
                    "selfEmail" => $user->getEmail(),
                    "selfBank" => $user->getBanque(),
                    "selfBic" => $user->getBic(),
                    "selfIban" => $user->getIban(),
                    "selfPremium" => ("paid" == $usersub["plan"]) ? true : false,
                    "sentDate" => $date,
                    "lines" => $lines,
                    "totalHT" => $totalHT,
                    "taxtotal" => $taxtotal,
                    "tax" => $tax,
                    "selfCompanyName" => $selfName,
                    "clientName" => $clientName,
                    "clientAddress" => $clientAdress,
                    "clientZipCode" => $clientZipCode,
                    "clientCity" => $clientCity,
                    "clientCountry" => $clientCountry,
                    "selfCompanyAdress" => $selfAdress,
                    "selfCompanyZipCode" => $selfZipCode,
                    "selfCompanyCity" => $selfCity,
                    "selfCompanyCountry" => $selfCountry,
                    "selfCompanyEmail" => $selfEmail,
                    "selfSiret" => $selfSiret,
                    "selfCompanyTel" => $selfTel,
                    "docNumber" => $docNumber,
                    "docType" => $docType,
                    "selfCompanyLogo" =>
                        $assetsManager->getUrl('uploads/user/company/'.$user->getCompanyLogoPath())

                ));
        }


        $mpdf->SetProtection(array('print'));
        $mpdf->WriteHTML($content, 0);
        $mpdf->SetTitle(strtolower($document->getNumber()."_".$document->getRefClient()->getCompanyName()));
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->SetDisplayMode('fullpage');
        return new Response($mpdf->Output());
    }

}
