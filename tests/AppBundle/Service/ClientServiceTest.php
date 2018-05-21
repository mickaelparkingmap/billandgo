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

namespace Tests\AppBundle\Service;

use AppBundle\Service\ClientService;
use BillAndGoBundle\Entity\Client;
use Tests\AppBundle\Utils\ClientTrait;
use Tests\AppBundle\Utils\PurgeTestCase;
use Tests\AppBundle\Utils\UserTrait;

class ClientServiceTest extends PurgeTestCase
{
    use UserTrait;
    use ClientTrait;


    public function testGetClient()
    {
        $user = $this->createUser();
        $this->save($user);
        $client = $this->createClient($user);
        $this->save($client);

        $service = new ClientService($this->getEntityManager());
        $clicli = $service->getClient($user, $client->getId());
        $this->assertNotNull($clicli);
        $this->assertTrue($clicli instanceof Client);
        $this->assertEquals($client->getCompanyName(), $clicli->getCompanyName());
    }

    public function testGetClientWrongId()
    {
        $user = $this->createUser();
        $this->save($user);
        $client = $this->createClient($user);
        $this->save($client);

        $service = new ClientService($this->getEntityManager());
        $clicli = $service->getClient($user, $client->getId() + 1);
        $this->assertNull($clicli);
    }


    public function testGetClientWrongUser()
    {
        $user = $this->createUser();
        $this->save($user);
        $user2 = $this->createUser([
            'firstname' => 'jean-paul',
            'email'     => 'jpdupont@gmail.com'
        ]);
        $this->save($user2);
        $client = $this->createClient($user);
        $this->save($client);

        $service = new ClientService($this->getEntityManager());
        $clicli = $service->getClient($user2, $client->getId());
        $this->assertNull($clicli);
    }

    public function testGetClientListFromUser()
    {
        $user = $this->createUser();
        $this->save($user);
        $service = new ClientService($this->getEntityManager());

        //0
        $list = $service->getClientListFromUser($user);
        $this->assertNotNull($list);
        $this->assertEmpty($list);

        //1
        $client = $this->createClient($user);
        $this->save($client);
        $list = $service->getClientListFromUser($user);
        $this->assertNotNull($list);
        $this->assertNotEmpty($list);
        $this->assertEquals(1, count($list));
        $this->assertEquals($client->getCompanyName(), $list[0]->getCompanyName());
    }
}
