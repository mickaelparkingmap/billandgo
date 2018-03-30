<?php

namespace MyJobManager\ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

use MyJobManagerBundle\Entity;

/**
 * AccessToken
 *
 * @ORM\Table(name="AccessToken")
 * @ORM\Entity
 */
class AccessToken extends BaseAccessToken
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Oauth_client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\User")
     */
    protected $user;

}