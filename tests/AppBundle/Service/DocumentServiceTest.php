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
use BillAndGoBundle\Entity\Document;
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
        $list = $documentService->listBillsFromUser($user);
        $this->assertNotNull($list);
        $this->assertEmpty($list);

        //bill
        $bill = $this->createBill($user);
        $this->save($bill);
        $list = $documentService->listBillsFromUser($user);
        $this->assertNotEmpty($list);
        $this->assertEquals(1, count($list));

        //estimate
        $estimate = $this->createDraw($user);
        $this->save($estimate);
        $list = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($list));
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testListBillsFromUserOptions()
    {
        $user = $this->createUser();
        $this->save($user);
        $documentService = new DocumentService($this->getEntityManager());

        //draw
        $bill = $this->createBill($user);
        $this->save($bill);
        $list = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["draw"]);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["canceled", "refused", "billed", "partially", "paid"]);
        $this->assertEmpty($list);

        //canceled
        $bill->setStatus("canceled");
        $this->save($bill);
        $list = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["canceled"]);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["draw", "refused", "billed", "partially", "paid"]);
        $this->assertEmpty($list);

        //refused
        $bill->setStatus("refused");
        $this->save($bill);
        $list = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["refused"]);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["draw", "canceled", "billed", "partially", "paid"]);
        $this->assertEmpty($list);

        //billed
        $bill->setStatus("billed");
        $this->save($bill);
        $list = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["billed"]);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["draw", "canceled", "refused", "partially", "paid"]);
        $this->assertEmpty($list);

        //partially
        $bill->setStatus("partially");
        $this->save($bill);
        $list = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["partially"]);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["draw", "canceled", "refused", "billed", "paid"]);
        $this->assertEmpty($list);

        //paid
        $bill->setStatus("paid");
        $this->save($bill);
        $list = $documentService->listBillsFromUser($user);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["paid"]);
        $this->assertEquals(1, count($list));
        $list = $documentService->listBillsFromUser($user, ["draw", "canceled", "refused", "billed", "partially"]);
        $this->assertEmpty($list);
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testListEstimateFromUser()
    {
        $user = $this->createUser();
        $this->save($user);

        $documentService = new DocumentService($this->getEntityManager());
        $list = $documentService->listDrawFromUser($user);
        $this->assertNotNull($list);
        $this->assertEmpty($list);

        //estimate
        $estimate = $this->createDraw($user);
        $this->save($estimate);
        $list = $documentService->listDrawFromUser($user);
        $this->assertEquals(1, count($list));

        //bill
        $bill = $this->createBill($user);
        $this->save($bill);
        $list = $documentService->listDrawFromUser($user);
        $this->assertNotEmpty($list);
        $this->assertEquals(1, count($list));
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetDocument() {

        $user1 = $this->createUser();
        $this->save($user1);
        $bill = $this->createBill($user1);
        $this->save($bill);
        $documentService = new DocumentService($this->getEntityManager());

        $document = $documentService->getDocument($user1, $bill->getId());
        $this->assertNotNull($document);
        $this->assertTrue($document instanceof Document);
        $this->assertEquals($bill->getNumber(), $document->getNumber());
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetDocumentWrongId() {

        $user1 = $this->createUser();
        $this->save($user1);
        $bill = $this->createBill($user1);
        $this->save($bill);
        $documentService = new DocumentService($this->getEntityManager());

        $document = $documentService->getDocument($user1, $bill->getId() + 1);
        $this->assertNull($document);
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetDocumentWrongUser() {

        $user1 = $this->createUser();
        $this->save($user1);
        $user2 = $this->createUser([
            'firstname' => 'jean-paul',
            'email'     => 'jpdupont@gmail.com'
        ]);
        $this->save($user2);
        $bill = $this->createBill($user1);
        $this->save($bill);
        $documentService = new DocumentService($this->getEntityManager());


        $document = $documentService->getDocument($user2, $bill->getId());
        $this->assertNull($document);
    }

}
