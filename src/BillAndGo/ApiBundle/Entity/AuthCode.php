<?php

namespace BillAndGo\ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;
use BillAndGoBundle\Entity;

/**
 * AuthCode
 *
 * @ORM\Table(name="AuthCode")
 * @ORM\Entity
 */
class AuthCode extends BaseAuthCode
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
     * @ORM\ManyToOne(targetEntity="BillAndGoBundle\Entity\User")
     */
    protected $user;

}