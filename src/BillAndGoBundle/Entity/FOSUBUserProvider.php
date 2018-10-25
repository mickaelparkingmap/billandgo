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


namespace BillAndGoBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FOSUBUserProvider extends BaseClass
{


    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        $userEmail = $this->userManager->findUserBy(array("username" => $response->getEmail()));
        //when the user is registrating

        if (null === $user && null === $userEmail) {
            $service = $response->getResourceOwner()->getName();
            $data = $response->getData();
            if (null !== $this->userManager->findUserBy(array("email" => $response->getEmail()))) {
                throw new NotFoundHttpException();
            }
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setRegisterDate(new \DateTime());
            $user->setRegisterType("github");
            $user->setUsername($response->getEmail());
            $user->setUsernameCanonical($response->getEmail());
            $user->setEmail($response->getEmail());
            $user->setEmailCanonical($response->getEmail());
            $user->setPassword($username);
            $user->setEnabled(true);
            $user->setCompanyname((null == $data["company"])? "Votre Entreprise" : $data["company"]);
            $this->userManager->updateUser($user);
            return $user;
        }
        elseif(null != $userEmail) {
            $session = new Session();
            $session->getFlashBag()->set("error", array("type" => "oauth_login", "existing account",
                "email" => $response->getEmail(), "github_id" => $username, "access_token" => $response->getAccessToken()));
            return ($user);
        }
        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        //update access token
        $user->$setter($response->getAccessToken());
        return $user;
    }
}