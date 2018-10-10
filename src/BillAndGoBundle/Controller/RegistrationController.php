<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BillAndGoBundle\Controller;

use BillAndGoBundle\Entity\UserOption;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Controller\SecurityController as FOSController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller managing the registration.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends FOSController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $user = $this->getUser();
        if (is_object($user)) { // || !$user instanceof UserInterface
           return ($this->redirectToRoute("billandgo_dashboard"));
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid() && $this->captchaVerify($request->get('g-recaptcha-response'))) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);



                if (null === $response = $event->getResponse()) {

                    $manager = $this->getDoctrine()->getManager();
                    $us = new UserOption();
                    $us->setUser($user);
                    $us->setName("pdf_bill_quote_choice");
                    $us->setValue("pdf.document.type.1");
                    $manager->persist($us);
                    $manager->flush();

                    $us1 = new UserOption();
                    $us1->setUser($user);
                    $us1->setName("user_free_plan");
                    $us1->setValue("active");
                    $manager->persist($us1);
                    $manager->flush();


                    $dateNow = new \DateTime();
                    $us2 = new UserOption();
                    $us2->setUser($user);
                    $us2->setName("user_free_plan_start");
                    $us2->setValue(($dateNow)->format("Y-m-d H:i:s"));
                    $manager->persist($us2);
                    $manager->flush();


                    $dateEdited = $dateNow->modify('+30 days');
                    $us3 = new UserOption();
                    $us3->setUser($user);
                    $us3->setName("user_free_plan_end");
                    $us3->setValue(($dateEdited)->format("Y-m-d H:i:s"));
                    $manager->persist($us3);
                    $manager->flush();


                    $mailer = $this->get("mailer");
                    $qb = $this->generateUrl("billandgo_dashboard", [], UrlGenerator::ABSOLUTE_URL);
                    $fin = $this->generateUrl("billandgo_paiment_index", [], UrlGenerator::ABSOLUTE_URL);
                    $cal = $this->generateUrl("billandgo_organizer_show", [], UrlGenerator::ABSOLUTE_URL);
                    $proj = $this->generateUrl("billandgo_project_list", [], UrlGenerator::ABSOLUTE_URL);
                    $message = (new \Swift_Message(ucfirst( "Bienvenue sur Bill&Go Service")))
                        ->setFrom(array('noreply@billandgo.fr' => "Bill&Go Service"))
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                            // app/Resources/views/Emails/registration.html.twig
                                'BillAndGoBundle:Registration:welcome.html.twig',
                                array('user' => $user, "qb" => $qb, "financial" => $fin, "calendar" => $cal,
                                    "proj" => $proj)
                            ),
                            'text/html'
                        )

                    ;

                    $mailer->send($message);


                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        #check if captcha response isn't get throw a message
            if($form->isSubmitted() &&  $form->isValid() &&
                !$this->captchaVerify($request->get('g-recaptcha-response'))){

                $form->get('job_type')->addError(new FormError("Vous devez valider le captcha avant de soumettre le formulaire'")
              );
            }


        return $this->render('@FOSUser/Registration/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Tell the user to check their email provider.
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->get('router')->generate('fos_user_registration_register'));
        }

        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render('@FOSUser/Registration/check_email.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');

            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed.
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

       /* $mailer = $this->get("mailer");
        $message = (new \Swift_Message(ucfirst( "Bienvenue sur Bill&Go Service")))
            ->setFrom('noreply@billandgo.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'BillAndGoBundle:Registration:welcome.html.twig',
                    array('user' => $user)
                ),
                'text/html'
            )

        ;

        $mailer->send($message);*/


        return $this->render('@FOSUser/Registration/confirmed.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession(),
        ));
    }


    /*public function testAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $qb = $this->generateUrl("billandgo_dashboard", [], UrlGenerator::ABSOLUTE_URL);
        $fin = $this->generateUrl("billandgo_paiment_index", [], UrlGenerator::ABSOLUTE_URL);
        $cal = $this->generateUrl("billandgo_organizer_show", [], UrlGenerator::ABSOLUTE_URL);
        $proj = $this->generateUrl("billandgo_project_list", [], UrlGenerator::ABSOLUTE_URL);

        return $this->render('BillAndGoBundle:Registration:welcome.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession(), "qb" => $qb, "financial" => $fin, "calendar" => $cal,
            "proj" => $proj
        ));
    }*



    /**
     * @return mixed
     */
    private function getTargetUrlFromSession()
    {
        $key = sprintf('_security.%s.target_path', $this->get('security.token_storage')->getToken()->getProviderKey());

        if ($this->get('session')->has($key)) {
            return $this->get('session')->get($key);
        }
    }

    # get success response from recaptcha and return it to controller
    public function captchaVerify(?string $recaptcha){
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret" => "6LeadXIUAAAAAIu1kU8xeuu7XkmA5ixFOCFlP_hz", "response" => $recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data->success;
    }
}
