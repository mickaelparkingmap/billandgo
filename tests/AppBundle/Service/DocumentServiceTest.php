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

use AppBundle\Service\DocumentService;
use BillAndGoBundle\Entity\User;
use Tests\AppBundle\Utils\DocumentTrait;
use Tests\AppBundle\Utils\PurgeTestCase;
use Tests\AppBundle\Utils\UserTrait;

class DocumentServiceTest extends PurgeTestCase
{
    use UserTrait;
    use DocumentTrait;

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testListBillsFromUser()
    {
        $user = $this->createUser();
        $this->save($user);

        $documentService = new DocumentService($this->getEntityManager());
        $listNull = $documentService->listBillsFromUser($user);
        $this->assertNotNull($listNull);
        $this->assertEmpty($listNull);

        $bill = $this->createBill($user);
        $this->save($bill);
        $listOne = $documentService->listBillsFromUser($user);
        $this->assertNotEmpty($listOne);
        $this->assertEquals(1, count($listOne));

        $draw = $this->createDraw($user);
        $this->save($draw);
        $listOne = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($listOne));
    }

    public function testListDrawFromUser()
    {
        $this->assertTrue(true);
    }

}
