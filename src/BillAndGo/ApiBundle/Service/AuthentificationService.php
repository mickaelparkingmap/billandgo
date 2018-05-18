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

namespace BillAndGo\ApiBundle\Service;

use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Repository\AccessTokenRepository;
use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use FOS\OAuthServerBundle\Model\Token;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthentificationService extends Controller
{

    /** @var EntityRepository $repo */
    private $repo;

    /**
     * AuthentificationService constructor.
     * @param EntityRepository $repo
     */
    public function __construct(
        EntityRepository $repo
    ) {
        $this->repo = $repo;
    }

    /**
     * @return null|string
     */
    private function getTokenFromHeaders ()
    {
        $token = null;
        $headers = apache_request_headers();
        $auth = $headers['Authorization'];
        if (null !== $auth) {
            $mode = substr($auth, 0, 6);
            if ("Bearer" === $mode) {
                $token = substr($auth, 7);
            }
        }
        return $token;
    }

    /**
     * @param string $tokenString
     * @return AccessToken
     */
    private function getTokenInDB (string $tokenString)
    {
        $token = null;
        $tokens = $this->repo->findBy([
            "token" => $tokenString
        ]);
        if ((is_array($tokens)) && (!empty($tokens)) && (isset($tokens[0]))) {
            $token = $tokens[0];
        }
        return $token;
    }

    /**
     * @return User
     */
    public function authenticate ()
    {
        $user = null;
        $tokenString = $this->getTokenFromHeaders();
        if (null !== $tokenString) {
            $token = $this->getTokenInDB($tokenString);
            if ((null !== $token) && (!$token->hasExpired())) {
                /** @var User $user */
                $user = $token->getUser();
            }
        }
        return $user;
    }
}