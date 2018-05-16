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

namespace Tests\AppBundle\Utils;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurgeTestCase extends KernelTestCase
{
    /** @var EntityManager $em */
    private $em;

    /**
     *
     */
    public static function setUpBeforeClass ()
    {
        parent::setUpBeforeClass();
        self::bootKernel();
    }

    /**
     *
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->purgeDatabase();
    }

    /**
     *
     */
    protected function tearDown ()
    {
        //not calling the parent
    }

    protected function getEntityManager () : EntityManager
    {
        return $this->em;
    }

    /**
     *
     */
    private function purgeDatabase () : void
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();
    }

    /**
     * @param $obj
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function save ($obj)
    {
        $this->em->persist($obj);
        $this->em->flush();
        return $obj;
    }

}