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
    use Symfony\Component\DependencyInjection\ContainerInterface;
    use Symfony\Component\Finder\Exception\AccessDeniedException;
    use Symfony\Component\Form\Form;
    use BillAndGoBundle\Form\LineEstimateType;
    use BillAndGoBundle\Form\LineBillType;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

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
        $begin = $request->get('begin');
        $end = $request->get('end');
        $estimates = $this->documentService->listDrawFromUser($user, $begin, $end);
        return $this->render(
            'BillAndGoBundle:document:index.html.twig',
            array(
                'list' => $estimates,
                'user' => $user,
                'type' => 'estimate',
                'limitation' =>  $this->getLimitation("quote")
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
        $begin = $request->get('begin');
        $end = $request->get('end');
        $bills = $this->documentService->listBillsFromUser($user, $begin, $end);

        return $this->render(
            'BillAndGoBundle:document:indexBill.html.twig',
            array(
                'list' => $bills,
                'user' => $user,
                'type' => 'bill',
                'limitation' =>  $this->getLimitation("bill")
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
                return $this->render(
                    'BillAndGoBundle:document:full.html.twig',
                    array(
                        'document'      => $document,
                        'taxes'         => $taxes,
                        'suggestions'   => $suggestions,
                        'form'          => $form->createView(),
                        'user'          => $user
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
        if (($id > 0) && (in_array($status, $this->status))) {
            $manager = $this->getDoctrine()->getManager();
            $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
            if ($document != null) {
                if ($document->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
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
                return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $id)));
            }
        }
        $ar404 = ["document doesn't exist"];
        return new Response(json_encode($ar404), 404);
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
            if ($document->getType()) {
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
                'user'      => $user
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
                    'user'      => $user
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
                'user'      => $user
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
        if (false === $this->getLimitation("bill")) {
            return $this->redirect($this->generateUrl("billandgo_limitation"));
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
         * @Method("GET")
         * @param int     $doc_id
         * @param Request $req    get request containing client_id
         * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
         */
    public function sendEmail(int $doc_id, Request $req)
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
        if ($document == null) {
            return $this->redirect($this->generateUrl("billandgo_bill_index"));
        }
        if ($contact == null) {
            return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $doc_id)));
        }
        if (($document->getRefUser() != $user) || ($document->getRefUser() != $user)) {
            $ar401 = ['wrong user'];
            return new Response(json_encode($ar401), 401);
        }

        $readableType = "";
        if ($document->getType()) {
            $type = "estimate";
            $readableType = "Devis";
            $rand = random_int(1, 1000000000);
        } else {
            $readableType = "Facture";
            $type = "bill";
            $rand = 0;
        }

        if ($user->getPlan() != "billandgo_paid_plan") {
            $sender = array('billandgo@iumio.com' => "Bill&Go Service");
        } else {
            $sender = array($user->getEmail() => ucfirst(strtolower($user->getFirstname()))
                . " ". ucfirst(strtolower($user->getLastname())));
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($readableType." : ".$document->getNumber()." de ".$user->getCompanyName())
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
                        "contact" => $contact
                    )
                ),
                "text/html"
            );
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
        if ($document->getType()) {
            return $this->redirectToRoute("billandgo_document_edit_status", array("id" => $doc_id, "status" => "estimated"));
        }
        return $this->redirectToRoute("billandgo_document_edit_status", array("id" => $doc_id, "status" => "billed"));
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
            return new Response("404");
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
        return new Response("401");
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


    /**
         * @Route("document/{id}/generate", name="billandgo_document_generate")
         * @Method("GET")
         * @param int $id
         * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
         */
    public function generateDocumentAction(int $id)
    {
        $manager = $this->getDoctrine()->getManager();
        $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($document != null) {
            $manager = $this->getDoctrine()->getManager();
            if ($document->getRefUser() != $user) {
                $ar401 = ["wrong user"];
                return new Response(json_encode($ar401), 401);
            }

            $document = $manager->getRepository('BillAndGoBundle:Document')->find($id);

            $taxes = $manager->getRepository('BillAndGoBundle:Tax')->findAll();
            $suggestions = $this->suggestionService->getList($user);
        }

        $companyName = $user->getCompanyname();
        $companyAdress = $user->getAddress()."<br>".$user->getZipCode()." ".$user->getCity()." ".$user->getCountry();

        $companySite = $user->getEmail();
        $companyTel = $user->getMobile();

        $clientName = $document->getRefClient()->getCompanyName();
        $clientAdress = $document->getRefClient()->getAdress()."<br>".
            $document->getRefClient()->getZipcode()." ".
            $document->getRefClient()->getCity()." ".
            $document->getRefClient()->getCountry();


        $factureNumber = $document->getNumber();

        //$document = new Document();
        $documenttype = ($document->isEstimate())? "Devis" : "Facture";

        $htmlpageheader = '<htmlpageheader name="myheader">
<table class="company">
<tr>
<td style="color:#0000BB;"><span style="font-weight: bold; font-size: 14pt;">'.$companyName.'</span>
<br />'.$companyAdress.'<br />'. $companySite .'<br />
<span style="font-family:dejavusanscondensed;">&#9742;</span>'. $companyTel .'</td>
<td style="text-align: right;">'.$documenttype.' .<br /><span style="font-weight: bold; font-size: 12pt;">'. $factureNumber .'</span></td>
</tr>
</table>
</htmlpageheader>';

        $htmlpagefooter = '<htmlpagefooter name="myfooter">
<div class="htmlpgfooter">
Page {PAGENO} of {nb}
</div>
</htmlpagefooter>';

        $date_time = $document->getSentDate();
        $intl_date_formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
        $date =  $intl_date_formatter->format($date_time);

        $money = ' &euro;';
        $workUnit = ' j/h';

        $element1 = 'Création de la charte graphique pour le web<br />Traitement des images fournies et adaptation.';
        $element1Quantity = 1;
        $element1UnitPrice = 1200;
        $element1FinalPrice = $element1Quantity * $element1UnitPrice;

        $element2 = 'Déclinaison de la charte dans les 2 versions de la langue (anglais / allemand).';
        $element2Quantity = 2;
        $element2UnitPrice = 250;
        $element2FinalPrice = $element2Quantity * $element2UnitPrice;

        $element3 = 'Intégration dans le site (3 versions de langue)';
        $element3Quantity = 4;
        $element3UnitPrice = 250;
        $element3FinalPrice = $element3Quantity * $element3UnitPrice;

        $element4 = 'Développement des pages, intégration des contenus, mis en page des rubriques statiques';
        $element4Quantity = 37;
        $element4UnitPrice = 100;
        $element4FinalPrice = $element4Quantity * $element4UnitPrice;

        $element5 = 'Forum de type PHPBB';
        $element5Quantity = 1;
        $element5UnitPrice = 250;
        $element5FinalPrice = $element5Quantity * $element5UnitPrice;

        $element6 = 'Espaces membres';
        $element6Quantity = 2;
        $element6UnitPrice = 250;
        $element6FinalPrice = $element6Quantity * $element6UnitPrice;

        $element7 = 'Module actualités';
        $element7Quantity = 1;
        $element7UnitPrice = 300;
        $element7FinalPrice = $element7Quantity * $element7UnitPrice;

        $element8 = 'Gestion des abonnements et désabonnements automatiques<br /> Interface pour la création des messages.';
        $element8Quantity = 1;
        $element8UnitPrice = 300;
        $element8FinalPrice = $element8Quantity * $element8UnitPrice;

        $element9 = 'Intégration des contenus en version anglaise et allemande';
        $element9Quantity = 6;
        $element9UnitPrice = 250;
        $element9FinalPrice = $element9Quantity * $element9UnitPrice;

        $element10 = '720 Plan';
        $element10Quantity = 1;
        $element10UnitPrice = 220;
        $element10FinalPrice = $element10Quantity * $element10UnitPrice;

        $element11 = 'Optimisation des pages pour une bonne visibilité des moteurs de recherche <br />
Inscription manuelle du site dans les principaux moteurs de recherche <br />
Inscription dans les annuaires thématiques gratuit ou Allo Pass.';
        $element11Quantity = 3;
        $element11UnitPrice = 200;
        $element11FinalPrice = $element11Quantity * $element11UnitPrice;

        $element = $element1FinalPrice + $element2FinalPrice + $element3FinalPrice + $element4FinalPrice + $element5FinalPrice + $element6FinalPrice +
            $element7FinalPrice + $element8FinalPrice + $element9FinalPrice + $element10FinalPrice + $element11FinalPrice;

        $elementTva = $element * 0.2;

        $elementFinal = $element - $elementTva;


        $line = '';

        $totalHT = 0;
        $tax = "";
        $taxtotal = 0;

        $lines = ($document->isEstimate())? $document->getRefLines() : $document->getRefLinesB();
        foreach ($lines as $one) {
            //$one = new Line();
            $line .= '<tr>
                <td align="center">'.$one->getQuantity().'</td>
                <td>'.$one->getName() . " : ".$one->getDescription().'</td>
                <td class="cost">'.$one->getPrice().'€</td>
                <td class="cost" colspan="2">'.$one->getQuantity()*$one->getPrice().'€</td>
                </tr>';
            $totalHT += $one->getQuantity()*$one->getPrice();
            $taxtotal += ($one->getRefTax()->getPercent() * ($one->getQuantity()*$one->getPrice())) / 100;
            $tax =  $one->getRefTax()->getName();
        }


        //$totalTax = line.refTax.percent * $totalHT / 100;//exit();
                    $content = '
            <!-- ITEMS HERE -->
            '.
                        $line.'
            <!-- END ITEMS HERE -->
            <tr>
            <td class="blanktotal" colspan="1" rowspan="3"></td>
            
            <td class="totals " colspan="2">Sous total:</td>
            <td class="totals cost" colspan="2">'.$totalHT.'€</td>
            </tr>
            <tr>
            <td class="totals" colspan="2">'.$tax.' :</td>
            <td class="totals cost" colspan="2">'.$taxtotal.'€</td>
            </tr>
            <tr>
            <td class="totals" colspan="2"><b>TOTAL:</b></td>
            <td class="totals cost" colspan="2"><b>'.($totalHT + $taxtotal).'€</b></td>
            </tr>
            ';

                    $html = '
            <html>
            <head>
            </head>
            <body>'.
                        '<!--mpdf'.$htmlpageheader.$htmlpagefooter.'
            
            <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
            <sethtmlpagefooter name="myfooter" value="on" />
            mpdf-->
            
            
            <div style="text-align: right">Date: '.$date.'</div><br />
            
            <table class="client" cellpadding="10">
            <tr>
            <td><span style="font-size: 10pt; color: #555555; font-family: sans;">CLIENT :</span>
            <br />'.$clientName.'<br />'.$clientAdress.'<br />
            </td>
            </tr>
            </table><br />
            
            <p class="projectName">'.$documenttype.' </p><br />
            <table class="items" cellpadding="10">
            <thead>
            <tr>
            <td width="15%">Quantite</td>
            <td width="50%">Description</td>
            <td width="15%">Prix Unit HT</td>
            <td width="20%" colspan="2">Prix Total HT</td>
            </tr>
            </thead>
            <br>
            <br>
            <div style="text-align: right"><strong>Coordonnées bancaires : </strong>:<br> '.$date.'</div><br />
            <tbody>
            '.$content.'
            </tbody>
            </table>
            </body>
            </html>
            ';
        $path = (getenv('MPDF_ROOT')) ? getenv('MPDF_ROOT') : __DIR__;
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 40,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);
        $stylesheet = file_get_contents(__DIR__.'/../../../pdf_generation/styles.css');

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(strtolower($document->getNumber()."_".$document->getRefClient()->getCompanyName()));
        //$mpdf->SetAuthor("BillandGo.");
        //$mpdf->SetWatermarkText("Payé");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html, 0);
        return new Response($mpdf->Output());
    }
}
