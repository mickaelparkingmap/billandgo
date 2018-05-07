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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BillAndGoBundle\Form\LineType;
use Symfony\Component\Validator\Constraints\Date;

class LinesController extends Controller
{
    /**
     * @Route("/lines", name="billandgo_testlines")
     */
    public function listAction()
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
            $ar401 = ["not connected"];
            return new Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        $list = $manager->getRepository('BillAndGoBundle:Line')->findByRefUser($user);
        return $this->render('BillAndGoBundle:Lines:list.html.twig', array(
            'list' => $list,
            'user' => $user
        ));
    }

    /**
     * create a bill if post request
     * else return the form to create one
     *
     * @Route("/lines/add", name="billandgo_line_add")
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

        $line = new Line();
        $form = $this->createForm(LineType::class, $line, array('uid' => $user->getId()));
        if ($req->isMethod('POST')) {
            if ($form->handleRequest($req)->isValid()) {
                $line->setRefUser($user);
                $line->setChronoTime(0);
                $manager->persist($line);
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_testlines"));
            }
        }
        return $this->render('BillAndGoBundle:Lines:add.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Route("/lines/{id}/edit/status/{status}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id id of the line
     * @param String $status the new status
     */
    public function editStatusAction($id, $status)
    {
        $avalaible_status = [
            "draw", "estimated", "accepted", "planned", "working", "waiting", "validated", "billing", "billed", "canceled"
        ];

        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if (($id > 0) && (in_array($status, $avalaible_status))){
            $manager = $this->getDoctrine()->getManager();
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line == NULL) {
                $ar404 = ["doesn't exist"];
                return new Response(json_encode($ar404), 404);
            }
            if ($line->getRefUser() != $user) {
                $ar401 = ["unauthorised"];
                return new Response(json_encode($ar401), 401);
            }
            $line->setStatus($status);
            $manager->flush();
            return $this->redirect($this->generateUrl("billandgo_testlines"));
        }
        $ar404 = ["doesn't exist"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/edit/deadline/{deadline}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id of the line
     * @param String $deadline new deadline
     */
    public function editDeadLineAction($id, $deadline)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line == NULL) {
                $ar404 = ["doesn't exist"];
                return new Response(json_encode($ar404), 404);
            }
            if ($line->getRefUser() != $user) {
                $ar401 = ["unauthorised"];
                return new Response(json_encode($ar401), 401);
            }
            $dl = new \DateTime($deadline);
            $line->setDeadline($dl);
            $manager->flush();
            return $this->redirect($this->generateUrl("billandgo_testlines"));
        }
        $ar404 = ["doesn't exist"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/edit/chronotime/{add}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id of the line
     * @param int $add hours added to the chrono time of the line
     */
    public function editChronoTimeAction($id, $add)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line == NULL) {
                $ar404 = ["doesn't exist"];
                return new Response(json_encode($ar404), 404);
            }
            if ($line->getRefUser() != $user) {
                $ar401 = ["unauthorised"];
                return new Response(json_encode($ar401), 401);
            }
            $oldTime = $line->getChronoTime();
            $line->setChronoTime($oldTime + $add);
            $manager->flush();
            return $this->redirect($this->generateUrl("billandgo_testlines"));
        }
        $ar404 = ["doesn't exist"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/edit/estimatedtime/{estim}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id of the line
     * @param int $estim new estimated time
     */
    public function editEstimatedTimeAction($id, $estim)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line == NULL) {
                $ar404 = ["doesn't exist"];
                return new Response(json_encode($ar404), 404);
            }
            if ($line->getRefUser() != $user) {
                $ar401 = ["unauthorised"];
                return new Response(json_encode($ar401), 401);
            }
            $line->setEstimatedTime($estim);
            $manager->flush();
            return $this->redirect($this->generateUrl("billandgo_testlines"));
        }
        $ar404 = ["doesn't exist"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/edit/client/{id_client}")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id of the line
     * @param int $id_client id of the client to which you want to ref the line
     */
    public function editClientAction ($id, $id_client)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if (($id > 0) && ($id_client > 0)) {
            $manager = $this->getDoctrine()->getManager();
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id_client);
            if ($line == NULL) {
                $ar404 = ["line doesn't exist"];
                return new Response(json_encode($ar404), 404);
            }
            $client = $manager->getRepository('BillAndGoBundle:Client')->find($id);
            if ($client == NULL) {
                $ar404 = ["client doesn't exist"];
                return new Response(json_encode($ar404), 404);
            }
            if (($line->getRefUser() != $user) || ($client->getUserRef() != $user)) {
                $ar401 = ["unauthorised"];
                return new Response(json_encode($ar401), 401);
            }
            $line->setRefClient($client);
            $manager->flush();
            return $this->redirect($this->generateUrl("billandgo_testlines"));
        }
        $ar404 = ["doesn't exist"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/cancel")
     * @param int $id id of the line
     */
    public function cancelAction($id)
    {
        $this->editStatusAction($id, "canceled");
    }

    /**
     * @Route("/lines/{id}/split/{nb}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id id of the line
     * @param int $nb the quantity to move to a new line
     */
    public function splitAction($id, $nb)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        if ($id > 0) {
            $manager = $this->getDoctrine()->getManager();
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line == NULL) {
                $ar404 = ["doesn't exist"];
                return new Response(json_encode($ar404), 404);
            }
            if ($line->getRefUser() != $user) {
                $ar401 = ["unauthorised"];
                return new Response(json_encode($ar401), 401);
            }
            if ($line->getQuantity() > $nb) {
                $new_line = new Line();
                $new_line->setChronoTime(0);
                $new_line->setRefUser($user);
                $new_line->setDeadLine($line->getDeadLine());
                $new_line->setDescription($line->getDescription());
                $new_line->setEstimatedTime($line->getestimatedTime());
                $new_line->setName($line->getName());
                $new_line->setPrice($line->getPrice());
                $new_line->setQuantity($nb);
                $new_line->setRefClient($line->getRefClient());
                $new_line->setStatus($line->getStatus());
                $manager->persist($new_line);
                $line->setQuantity($line->getQuantity() - $nb);
                $manager->flush();
                return $this->redirect($this->generateUrl("billandgo_testlines"));
            }
            $ar500 = ["can't split"];
            return new Response(json_encode($ar500), 500);
        }
        $ar404 = ["doesn't exist"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/detach/estimate", name="billandgo_line_detach_estimate")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id id of the line
     */
    public function detach_from_estimate($id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        if ($id > 0) {
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line) {
                if ($line->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
                }
                $estim = $line->getRefEstimate()[0];
                if ($estim && in_array($estim->getStatus(), ["draw", "canceled"])) {
                    $estim->removeRefLine($line);
                    $line->removeRefEstimate($estim);
                    if (!($line->getRefEstimate()[0]) && !($line->getRefProject()[0]) && !($line->getRefBill()[0])) {
                        $manager->remove($line);
                    }
                    $manager->flush();
                    return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $estim->getId())));
                }
                $ar404 = ["no estimate"];
                return new Response(json_encode($ar404), 404);
            }
        }
        $ar404 = ["no line"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/detach/project", name="billandgo_line_detach_project")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id id of the line
     */
    public function detach_from_project($id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        if ($id > 0) {
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line) {
                if ($line->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
                }
                $project = $line->getRefProject()[0];
                if ($project) {
                    $project->removeRefLine($line);
                    $line->removeRefProject($project);
                    if (!($line->getRefEstimate()[0]) && !($line->getRefProject()[0]) && !($line->getRefBill()[0])) {
                        $manager->remove($line);
                    }
                    $manager->flush();
                    return $this->redirect($this->generateUrl("billandgo_project_view", array('id' => $project->getId())));
                }
                $ar404 = ["no estimate"];
                return new Response(json_encode($ar404), 404);
            }
        }
        $ar404 = ["no line"];
        return new Response(json_encode($ar404), 404);
    }

    /**
     * @Route("/lines/{id}/detach/bill", name="billandgo_line_detach_bill")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @param int $id id of the line
     */
    public function detach_from_bill($id)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            $ar401 = ["disconnected"];
            return new Response(json_encode($ar401), 401);
        }
        $manager = $this->getDoctrine()->getManager();
        if ($id > 0) {
            $line = $manager->getRepository('BillAndGoBundle:Line')->find($id);
            if ($line) {
                if ($line->getRefUser() != $user) {
                    $ar401 = ["wrong user"];
                    return new Response(json_encode($ar401), 401);
                }
                $bill = $line->getRefBill()[0];
                if ($bill) {
                    $bill->removeRefLinesB($line);
                    $line->removeRefBill($bill);
                    if (!($line->getRefEstimate()[0]) && !($line->getRefProject()[0]) && !($line->getRefBill()[0])) {
                        $manager->remove($line);
                    }
                    $manager->flush();
                    return $this->redirect($this->generateUrl("billandgo_document_view", array('id' => $bill->getId())));
                }
                $ar404 = ["no estimate"];
                return new Response(json_encode($ar404), 404);
            }
        }
        $ar404 = ["no line"];
        return new Response(json_encode($ar404), 404);
    }



    /**
     * @Route("/lines/{id1}/join/{id2}")
     */
    public function joinAction($id1, $id2)
    {
        return $this->render('BillAndGoBundle:Lines:join.html.twig', array(
            // ...
        ));
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
