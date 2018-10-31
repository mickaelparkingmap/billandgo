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

use AppBundle\Service\GithubClientService;
use AppBundle\Service\ProjectService;
use BillAndGoBundle\Entity\Line;
use BillAndGoBundle\Entity\Project;
use BillAndGoBundle\Entity\Tax;
use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use BillAndGoBundle\Form\ProjectType;
use BillAndGoBundle\Form\LineProjectType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProjectController extends Controller
{

    /** @var ProjectService $projectService */
    private $projectService;

    /** @var GithubClientService $githubClientService */
    private $githubClientService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->projectService = $this->get("AppBundle\Service\ProjectService");
        $this->githubClientService = $this->get("AppBundle\Service\GithubClientService");
    }
    /**
     * list all projects
     *
     * @Route("/projects", name="billandgo_project_list")
     * @return             \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
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
        $list_project = $this->projectService->getProjectList($user);
        return $this->render(
            'BillAndGoBundle:Project:index.html.twig',
            array(
                'list' => $list_project,
                'user' => $user,
                'usersub' => $usersub
            )
        );
    }

    /**
     * render a project and its lines
     *
     * @Route("/projects/{id}", name="billandgo_project_view", requirements={"id" = "\d+"});
     * @param                   Request $req post request of split, line creation or line edition
     * @param                   int     $id  id of the project
     * @return                  \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        if ($id > 0) {
            $project = $this->projectService->getProject($user, $id);
            if ($project != null) {
                $manager = $this->getDoctrine()->getManager();
                $line = new Line();
                $form = $this->get('form.factory')->create(LineProjectType::class, $line);

                if ($req->isMethod('POST')) {
                    if ($this->viewPostAction($id, $req, $user, $project, $form, $line) < 0) {
                        $ar500 = ["wrong request"];
                        return new Response(json_encode($ar500), 500);
                    } else {
                        $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
                    }
                }
                $taxes = $manager->getRepository(Tax::class)->findAll();
                return $this->render(
                    'BillAndGoBundle:Project:full.html.twig',
                    array(
                        'project' => $project,
                        'taxes' => $taxes,
                        'form' => $form->createView(),
                        'user' => $user,
                        'usersub' => $usersub
                    )
                );
            }
        }
        return $this->redirect($this->generateUrl("billandgo_project_list"));
    }

    /**
     * call by viewAction
     * edit, add or split lines in the project
     *
     * @param  int     $id      id of current project
     * @param  Request $req     request sent to viewAction
     * @param  User    $user    current user
     * @param  Project $project current project
     * @param  Form    $form    form LineProjectType
     * @param  Line    $line    new line if creation
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
            if ($this->splitLine($id, $id_line, $split, $user) != 0) {
                return -500;
            } else {
                return 1;
            }
        } elseif (($id_line) && ($edit['name']) && ($edit['description'])
            && ($edit['quantity']) && ($edit['price'])
            && ($edit['estim']) && ($edit['chrono']) && ($edit['deadline'])
        ) {
            if ($this->editLine($id_line, $edit, $user) != 0) {
                return -500;
            } else {
                return 2;
            }
        } elseif ($form->handleRequest($req)->isValid()) {
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
     * @param                  Request $req
     * @return                 \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $req)
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
        $sync = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "sync_task_calendar"));
        return $this->render(
            'BillAndGoBundle:Project:add.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user,
                'usersub' => $usersub,
                'syncTask' => (null === $sync)? "inactive" : $sync->getValue()
            )
        );
    }

    /**
     * create a project from an estimate
     *
     * @Route("/projects/create/estimate/{estimateID}", name="billandgo_project_create_from_estimate")
     * @param                                           Request $req
     * @param                                           int     $estimateID
     * @return                                          \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response     *
     */
    public function addFromEstimateAction(Request $req, int $estimateID)
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

        $manager = $this->getDoctrine()->getManager();
        $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimateID);
        if (!($estimate) || !($estimate->isEstimate())) {
            $ar404 = ["doesn't exist or is not an estimate"];
            return new Response(json_encode($ar404), 404);
        }
        if ($estimate->getRefUser() != $user) {
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
        /** @var Line $line */
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
     * @Route("/projects/create/lines", name="billandgo_project_create_from_lines")
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addFromLines(Request $req)
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
        $estimate = $manager->getRepository('BillAndGoBundle:Document')->find($estimate_id);
        if ($estimate->getRefUser() != $user) {
            $ar401 = ['wrong user'];
            return new Response(json_encode($ar401), 401);
        }
        $project = new Project();
        $project->setName($name);
        $project->setDescription($description);
        $project->setRefUser($user);
        $project->setRefClient($estimate->getRefClient());
        $project->setBegin(new \DateTime());
        if (isset($deadline)) {
            $project->setDeadline(new \DateTime($deadline));
        }
        foreach ($lines_id as $line_id) {
            /** @var Line $line */
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
     * @param                                      int $id      id of the project
     * @param                                      int $id_line id of the line
     * @return                                     \Symfony\Component\HttpFoundation\Response
     */
    public function addLineAction($id, $id_line)
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
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
            if (($project != null) && ($line != null)) {
                if (($project->getRefUser() != $user) || ($line->getRefUser() != $user)) {
                    $ar401 = ["not your project or line"];
                    return new Response(json_encode($ar401), 401);
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
     * @param                                         int $id      id of the project
     * @param                                         int $id_line id of the line to delete
     * @return                                        \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLineAction($id, $id_line)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
            if (($project != null) && ($line != null)) {
                if (($project->getRefUser() != $user) || ($line->getRefUser() != $user)) {
                    $ar401 = ["not your project or line"];
                    return new Response(json_encode($ar401), 401);
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
     * @param  int  $id      id of the project
     * @param  int  $id_line id of the line to split
     * @param  int  $split   quantity to split : will be remove from the existing line and attributed to the new one
     * @param  User $user    current user
     * @return int
     */
    private function splitLine($id, $id_line, $split, $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $project = $manager->getRepository('BillAndGoBundle:Project')->find($id);
        $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_line);
        if (($line != null) && ($line->getQuantity() > $split)) {
            if (($line->getRefUser() != $user) || ($project->getRefUser() != $user)) {
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
        $line->setEstimatedTime($edit['estim']);
        $line->setChronoTime($edit['chrono']);
        $line->setDeadline(new \DateTime($edit['deadline']));
        $manager->flush();
        return 0;
    }


    /**
     *
     * @Route("projects/{id}/edit/save", name="billandgo_project_edit_save")
     * @Method("POST")
     * @param int $id
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editDesc(int $id, Request $request)
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
            $project = $this->getDoctrine()->getRepository('BillAndGoBundle:Project')->
            findOneBy(["id" => $id, "refUser" => $user->getId()]);
            if ($project == null) {
                throw new NotFoundHttpException("Projet non trouvée");
            }

            //$document = new Document();
            $project->setDescription($desc);
            $manager->merge($project);
            $manager->flush();
            return new JsonResponse(["OK"]);
        }
        throw new NotFoundHttpException("Valeur Description nulle");
    }

    /**
     * edit status of a line
     *
     * @param int    $id      id of the project
     * @param int    $id_line id of the line to edit
     * @param String $status  new status : draw, estimated, accepted, planned, working, waiting, validated, billing, billed, canceled
     * @Route("/projects/{id}/line/{id_line}/edit/status/{status}", name="billandgo_project_line_edit_status")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editLineStatus($id, $id_line, $status)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('disconnected');
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
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
            if (($project != null) && ($line != null)) {
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

    /**
     * @Route("projects/{id}/create_repo", name="billandgo_project_create_repo")
     * @Method("POST")
     *
     * @param int       $id
     * @param Request   $request
     * @return Response
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function createRepo(int $id, Request $request): Response
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('disconnected');
        }
        $repoName = $request->get("name");
        if (!$repoName) {
            throw new \Exception("missing name parameter");
        }
        $public = (null === $request->get("private"));
        $this->projectService->createRepo($user, $id, $repoName, $public);

        return $this->redirect($this->generateUrl("billandgo_project_view", ["id" => $id]));
    }

    /**
     * @Route("projects/{id}/set_repo/", name="billandgo_project_set_repo")
     * @Method("POST")
     *
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function setRepo(int $id, Request $request): Response
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('disconnected');
        }
        $repoName = $request->get("name");
        if (!$repoName) {
            throw new \Exception("missing name parameter");
        }
        $this->projectService->setRepo($user, $id, $repoName);

        return $this->redirect($this->generateUrl("billandgo_project_view", ["id" => $id]));
    }

    /**
     * @Route("projects/{id}/list_repo", name="billandgo_project_list_repo")
     * @Method("GET")
     *
     * @return Response
     * @throws \Exception
     */
    public function listOfRepo(): Response
    {
        $user = $this->getUser();
        $repos = $this->githubClientService->listOfRepo($user);
        $returnList = [];
        foreach ($repos as $repo) {
            $returnList[] = [
                "name"          => $repo["name"],
                "full_name"     => $repo["full_name"],
                "private"       => $repo["private"],
                "created_at"    => $repo["created_at"],
                "updated_at"    => $repo["updated_at"]
            ];
        }

        return new Response(json_encode($returnList));
    }
}
