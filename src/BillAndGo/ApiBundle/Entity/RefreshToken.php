<?php

namespace BillAndGo\ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;
use BillAndGoBundle\Entity;

/**
 * RefreshToken
 *
 * @ORM\Table(name="RefreshToken")
 * @ORM\Entity
 */
class RefreshToken extends BaseRefreshToken
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