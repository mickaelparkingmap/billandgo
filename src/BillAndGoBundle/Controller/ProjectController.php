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

use BillAndGoBundle\Entity\Line;
use BillAndGoBundle\Entity\Project;
use BillAndGoBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use BillAndGoBundle\Form\ProjectType;
use BillAndGoBundle\Form\LineProjectType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProjectController extends Controller
{
    /**
     * list all projects
     *
     * @Route("/projects", name="billandgo_project_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }

        $manager = $this->getDoctrine()->getManager();
        $list_project = $manager->getRepository('BillAndGoBundle:Project')->findByRefUser($user);
        return $this->render('BillAndGoBundle:Project:index.html.twig', array(
            'list' => $list_project,
            'user' => $user,
            'limitation' =>  $this->getLimitation("project")
        ));
    }

    /**
     * render a project and its lines
     *
     * @Route("/projects/{id}", name="billandgo_project_view", requirements={"id" = "\d+"});
     * @param Request $req post request of split, line creation or line edition
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
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
            if ($project != NULL) {
                if ($project->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
                }
                $line = new Line();
                $form = $this->get('form.factory')->create(LineProjectType::class, $line);

                if ($req->isMethod('POST')) {
                    if ($this->viewPostAction($id, $req, $user, $project, $form, $line) < 0){
                        $ar500 = ["wrong request"];
                        return new Response(json_encode($ar500), 500);
                    }
                    else
                        $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
                }
                $taxes = $manager->getRepository('BillAndGoBundle:Tax')->findAll($id);
                return $this->render('BillAndGoBundle:Project:full.html.twig', array(
                    'project' => $project,
                    'taxes' => $taxes,
                    'form' => $form->createView(),
                    'user' => $user
                ));
            }
        }
        return $this->redirect($this->generateUrl("billandgo_project_list"));
    }

    /**
     * call by viewAction
     * edit, add or split lines in the project
     *
     * @param int $id id of current project
     * @param Request $req request sent to viewAction
     * @param User $user current user
     * @param Project $project current project
     * @param Form $form form LineProjectType
     * @param Line $line new line if creation
     * @return int
     */
    private function viewPostAction(int $id, Request $req, User $user, Project $project, Form $form, Line $line)
    {
        $manager = $this->getDoctrine()->getManager();
        $split = $req->request->get('split');
        $id_line = $req->request->get('id_line');
        $edit['name'] = $req->request->get('name');
        $edit['description'] = $req->request->get('description');
        $edit['quantity'] = $req->request->get('quantity');
        $edit['price'] = $req->request->get('price');
        $edit['refTax'] = $req->request->get('refTax');
        $edit['estim'] = $req->request->get('estimated_time');
        $edit['chrono'] = $req->request->get('chrono_time');
        $edit['deadline'] = $req->request->get('deadLine');

        if (($split) && ($id_line)) {
            if ($this->splitLine($id, $id_line, $split, $user) != 0)
                return -500;
            else
                return 1;
        }

        elseif (($id_line) && ($edit['name']) && ($edit['description'])
            && ($edit['quantity']) && ($edit['price'])
            && ($edit['estim']) && ($edit['chrono']) && ($edit['deadline']))
        {
            if ($this->editLine($id_line, $edit, $user) != 0)
                return -500;
            else
                return 2;
        }

        elseif ($form->handleRequest($req)->isValid())
        {
            $line->setStatus("planned");
            $line->setRefUser($user);
            $line->setRefClient($project->getRefClient());
            $project->addRefLine($line);
            $manager->persist($line);
            $manager->flush();
            return 3;
        }
        return 0;
    }

    /**
     * add a project
     *
     * @Route("/projects/add", name="billandgo_project_add")
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
        }
        if (false === $this->getLimitation("project")) {
            return $this->redirect($this->generateUrl("billandgo_limitation"));
        }
        $manager = $this->getDoctrine()->getManager();
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, array('uid' => $user->getId()));
        if ($req->isMethod('POST')) {
            if (($form->handleRequest($req)->isValid())) {
                $project->setRefUser($user);
                $manager->persist($project);
                $project->setBegin(new \DateTime());//souci de format
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_project_view", array('id' => $project->getId())));
            }
        }
        return $this->render('BillAndGoBundle:Project:add.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * create a project from an estimate
     *
     * @Route("/projects/create/estimate/{estimateID}", name="billandgo_project_create_from_estimate")
     * @param Request $req
     * @param int $estimateID
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response     *
     */
    public function addFromEstimateAction (Request $req, int $estimateID) {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
        }

        if (false === $this->getLimitation("project")) {
            return $this->redirect($this->generateUrl("billandgo_limitation"));
        }

        $manager = $this->getDoctrine()->getManager();
        $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimateID);
        if (!($estimate) || !($estimate->getType())) {
            $ar404 = ["doesn't exist or is not an estimate"];
            return new Response(json_encode($ar404), 404);
        }
        if ($estimate->getRefUser() != $user)
        {
            $ar401 = ['wrong user'];
            return new Response(json_encode($ar401), 401);
        }
        $project = new Project();
        $project->setName($req->request->get('name'));
        $project->setDeadline(new \DateTime($req->request->get("deadline")));
        $project->setRefUser($user);
        $project->setBegin(new \DateTime());
        $project->setDescription($estimate->getDescription());
        $project->setRefClient($estimate->getRefClient());
        foreach ($estimate->getRefLines() as $line) {
            $project->addRefLine($line);
            $line->addRefProject($project);
            $line->setStatus("planned");
        }
        $manager->persist($project);
        $manager->flush();
        return $this->redirect($this->generateUrl("billandgo_project_view", array('id' => $project->getId())));
    }

    /**
     * @Route("/project/create/lines", name="billandgo_project_create_from_lines")
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addFromLines (Request $req)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
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
        $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimate_id);
        if ($estimate->getRefUser() != $user)
        {
            $ar401 = ['wrong user'];
            return new Response(json_encode($ar401), 401);
        }
        $project = new Project();
        $project->setName($name);
        $project->setDescription($description);
        $project->setRefUser($user);
        $project->setRefClient($estimate->getRefClient());
        $project->setBegin(new \DateTime());
        if (isset($deadline))
            $project->setDeadline(new \DateTime($deadline));
        foreach ($lines_id as $line_id) {
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($line_id);
            if ($line->getRefUser() != $user) {
                $ar401 = ['wrong user'];
                return new Response(json_encode($ar401), 401);
            }
            $line->setStatus("planned");
            $project->addRefLine($line);
            $line->addRefProject($project);
            $manager->persist($line);
        }
        $project->addRefDocument($estimate);
        $manager->persist($project);
        $manager->flush();
        return $this->redirect($this->generateUrl("billandgo_project_view", array('id' => $project->getId())));

    }

    /**
     * link an existing line to a project
     *
     * @Route("/projects/{id}/line/{id_line}/add")
     * @param int $id id of the project
     * @param int $id_line id of the line
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addLineAction($id, $id_line) {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
            if (($project != NULL) && ($line != NULL)) {
                if (($project->getRefUser() != $user) || ($line->getRefUser() != $user)) {
                    $ar401 = ["not your project or line"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                $project->addRefLine($line);
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_project_view", array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl("billandgo_project_list"));
    }

    /**
     * delete a line
     *
     * @Route("/projects/{id}/line/{id_line}/delete", name="billandgo_project_line_delete")
     * @param int $id id of the project
     * @param int $id_line id of the line to delete
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLineAction($id, $id_line) {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
            if (($project != NULL) && ($line != NULL)) {
                if (($project->getRefUser() != $user) || ($line->getRefUser() != $user)) {
                    $ar401 = ["not your project or line"];
                    return new \Symfony\Component\HttpFoundation\Response(json_encode($ar401), 401);
                }
                $project->removeRefLine($line);
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_project_view", array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl("billandgo_project_list"));
    }

    /**
     * split line : create another line et reduce quantity of the first one
     * called by viewPostAction
     *
     * @param int $id id of the project
     * @param int $id_line id of the line to split
     * @param int $split quantity to split : will be remove from the existing line and attributed to the new one
     * @param User $user current user
     * @return int
     */
    private function splitLine ($id, $id_line, $split, $user) {
        $manager = $this->getDoctrine()->getManager();
        $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
        $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
        if (($line != NULL) && ($line->getQuantity() > $split)) {
            if (($line->getRefUser() != $user) || ($project->getRefUser() != $user))
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
            $project->addRefLine($new_line);
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
        $line->setEstimatedTime($edit['estim']);
        $line->setChronoTime($edit['chrono']);
        $line->setDeadline(new \DateTime($edit['deadline']));
        $manager->flush();
        return 0;
    }

    /**
     * edit status of a line
     *
     * @param int $id id of the project
     * @param int $id_line id of the line to edit
     * @param String $status new status : draw, estimated, accepted, planned, working, waiting, validated, billing, billed, canceled
     * @Route("/projects/{id}/line/{id_line}/edit/status/{status}", name="billandgo_project_line_edit_status")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editLineStatus ($id, $id_line, $status)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('disconnected');
        }
        $avalaible_status = [
            "draw", "estimated", "accepted",
            "planned", "working", "waiting", "validated",
            "billing", "billed",
            "canceled"
        ];
        if (($id > 0) && ($id_line > 0) && (in_array($status, $avalaible_status))) {
            $manager = $this->getDoctrine()->getManager();
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
            if (($project != NULL) && ($line != NULL)) {
                if (($project->getRefUser() == $user) && ($line->getRefUser() == $user)) {
                    $line->setStatus($status);
                    $manager->flush();
                    return $this->redirect($this->generateUrl("billandgo_project_view", array('id' => $id)));
                }
            }
        }
        $ar404 = ["doesn't exist"];
        return new Response(json_encode($ar404), 404);
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