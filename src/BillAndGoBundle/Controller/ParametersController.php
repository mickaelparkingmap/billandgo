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

use BillAndGoBundle\Entity\Line;
use BillAndGoBundle\Entity\UserOption;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BillAndGoBundle\Form\LineType;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class ParametersController
 * @package BillAndGoBundle\Controller
 */
class ParametersController extends Controller
{

    /** Get available PDF template
     * @return array Templates
     */
    public function getAvailableTemplate():array {
        $filepdf = [];
        $finder = new Finder();
        $finder->files()->in(__DIR__.DIRECTORY_SEPARATOR."../Resources/views/document/pdf");

        foreach ($finder as $file) {
            // dumps the relative path to the file
            $name =  str_replace(".html.twig", "", $file->getRelativePathname());
            $sname = strtoupper(str_replace(".", " ",
                str_replace("pdf.document.", "", $name)));
            $filepdf[] = ["realname" => $name, "shortname" => $sname];
        }
        if (!empty($filepdf)) {
            sort($filepdf);
        }
        $filepdf[] = ["realname" => "custom", "shortname" => "Personnalisé"];

        return ($filepdf);
    }
    /**
     * @Route("/parameters", name="billandgo_parameters_show")
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $manager = $this->getDoctrine()->getManager();

        $selectedTemplate = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_choice"));

        $selectedTemplateCustom = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom"));

        $sync = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "sync_task_calendar"));

        $selectedTemplateCustomStyle = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_style"));

        $selectedTemplateCustomHeader = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_header"));

        $selectedTemplateCustomBody = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_body"));

        $selectedTemplateCustomFooter = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_footer"));

        $filepdf = $this->getAvailableTemplate();

        if (null === $selectedTemplateCustom) {
            $pdftype = ((null === $selectedTemplate) ? "pdf.document.type.1" : $selectedTemplate->getValue());
        }
        else {
            $pdftype = "custom";
        }
        return $this->render(
            'BillAndGoBundle:Parameters:show_content.html.twig',
            array(
                'user' => $user,
                "filepdf" => $filepdf,
                'pdfchoice' => $pdftype,
                'usersub' => $usersub,
                'syncTask' => (null === $sync)? "inactive" : $sync->getValue(),
                'ctf' => null === $selectedTemplateCustomFooter? "" : $selectedTemplateCustomFooter->getValue(),
                'ctb' => null === $selectedTemplateCustomBody? "" : $selectedTemplateCustomBody->getValue(),
                'cth' => null === $selectedTemplateCustomHeader? "" : $selectedTemplateCustomHeader->getValue(),
                'cts' => null === $selectedTemplateCustomStyle? "" : $selectedTemplateCustomStyle->getValue(),
            )
        );
    }

    /**
     * @Route("/parameters/pdf/template/save", name="billandgo_parameters_pdf_template_save")
     */
    public function saveSelectedTemplate(Request $request) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $manager = $this->getDoctrine()->getManager();

        $selected = $request->get("pdfchoice");
        $pdf = $this->getAvailableTemplate();
        $u = 0;
        foreach ($pdf as $one => $value) {
            if ($selected === $value["realname"]) {
                $u++;
            }
        }

        if (0 === $u) {
            return new JsonResponse(["code" => 500, "msg" => "Ce type de template n'existe pas", 500]);
        }

        $selectedTemplate = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_choice"));

        $selectedTemplateCustom = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom"));

        $selectedTemplateCustomStyle = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_style"));

        $selectedTemplateCustomHeader = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_header"));

        $selectedTemplateCustomBody = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_body"));

        $selectedTemplateCustomFooter = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "pdf_bill_quote_custom_footer"));


        $style = $request->get("cpstyle");
        $header = $request->get("cpheader");
        $body = $request->get("cpbody");
        $footer = $request->get("cpfooter");

        if ("custom" !== $selected) {

            if (null === $selectedTemplate) {
                $us = new UserOption();  $us->setName("pdf_bill_quote_choice");
                $us->setValue($selected);
                $manager->persist($us);
                $manager->flush();
            }
            else {
                $selectedTemplate->setValue($selected);
                $manager->merge($selectedTemplate);
                $manager->flush();
            }

            if (null !== $selectedTemplateCustom) {
                $manager->remove($selectedTemplateCustom);
                $manager->flush();

                $manager->remove($selectedTemplateCustomStyle);
                $manager->flush();

                $manager->remove($selectedTemplateCustomHeader);
                $manager->flush();

                $manager->remove($selectedTemplateCustomBody);
                $manager->flush();

                $manager->remove($selectedTemplateCustomFooter);
                $manager->flush();
            }

        }
        elseif (null !== $selectedTemplateCustom) {

            $selectedTemplateCustomStyle->setValue($style);
            $manager->merge($selectedTemplateCustomStyle);
            $manager->flush();


            $selectedTemplateCustomHeader->setValue($header);
            $manager->merge($selectedTemplateCustomHeader);
            $manager->flush();


            $selectedTemplateCustomBody->setValue($body);
            $manager->merge($selectedTemplateCustomBody);
            $manager->flush();


            $selectedTemplateCustomFooter->setValue($footer);
            $manager->merge($selectedTemplateCustomFooter);
            $manager->flush();


        }
        else {
            $us = new UserOption();
            $us->setUser($user);
            $us->setName("pdf_bill_quote_custom");
            $us->setValue("active");

            $manager->persist($us);
            $manager->flush();

            //// STYLE
            $us1 = new UserOption();
            $us1->setUser($user);
            $us1->setName("pdf_bill_quote_custom_style");
            $us1->setValue($style);

            $manager->persist($us1);
            $manager->flush();


            //// HEADER
            $us1 = new UserOption();
            $us1->setUser($user);
            $us1->setName("pdf_bill_quote_custom_header");
            $us1->setValue($header);

            $manager->persist($us1);
            $manager->flush();

            //// BODT
            $us1 = new UserOption();
            $us1->setUser($user);
            $us1->setName("pdf_bill_quote_custom_body");
            $us1->setValue($body);

            $manager->persist($us1);
            $manager->flush();

            //// FOOTER
            $us1 = new UserOption();
            $us1->setUser($user);
            $us1->setName("pdf_bill_quote_custom_footer");
            $us1->setValue($footer);

            $manager->persist($us1);
            $manager->flush();
        }

        return new JsonResponse(["code" => 200, "msg" => "Le template choisi a bien été sauvegardé", 200]);
    }


    /**
     * @Route("/parameters/calendar/task/save", name="billandgo_parameters_calendar_task_save")
     */
    public function saveCalendarTask(Request $request) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $usersub = DefaultController::userSubscription($user, $this);
        if ($usersub["remaining"] <= 0) {
            $this->addFlash("error", $usersub["msg"]);
            return ($this->redirectToRoute("fos_user_security_login"));
        }
        $manager = $this->getDoctrine()->getManager();

        $selected = $request->get("task");
        $sync = $manager->getRepository('BillAndGoBundle:UserOption')->findOneBy(
            array("user" => $user->getId(), "name" => "sync_task_calendar"));

        if (in_array($sync, ["active", "inactive"])) {
            return new JsonResponse(["code" => 500, "msg" => "Valeur non reconnue", 500]);
        }

        if (null !== $sync) {
            $sync->setValue($selected);
            $manager->merge($sync);
            $manager->flush();
        }

        else {
            $us = new UserOption();
            $us->setUser($user);
            $us->setName("sync_task_calendar");
            $us->setValue($selected);
            $manager->persist($us);
            $manager->flush();
        }


        return new JsonResponse(["code" => 200, "msg" => "Votre choix a bien été sauvegardé", 200]);
    }

}
