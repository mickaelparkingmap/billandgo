<?php

/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://www.billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */

namespace BillAndGoBundle\EventListener;

use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\Test\LoggerInterfaceTest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Tests\Controller\ContainerAwareController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    protected $entityManager = null;
    protected $container = null;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $request = $event->getRequest();
        $this->onAuthenticationSuccess($request, $token);
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        $gid = $request->get("_github_id");
        $token = $request->get("_access_token");
        if (null !== $gid && null !== $token && null != $user) {
            $user->setGithubId($gid);
            $user->setGithubAccessToken($token);

            $this->entityManager->merge($user);
            $this->entityManager->flush();
        }

        return new RedirectResponse($request->headers->get("referer"));
    }
}