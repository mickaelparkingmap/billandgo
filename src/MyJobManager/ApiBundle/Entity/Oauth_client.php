<?php


namespace MyJobManager\ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
* Oauth_client
*
 * @ORM\Table(name="Oauth_client")
* @ORM\Entity
*/
class Oauth_client extends BaseClient
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // TODO
    }

}