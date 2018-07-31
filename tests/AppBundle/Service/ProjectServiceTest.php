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

use AppBundle\Service\ProjectService;
use BillAndGoBundle\Entity\Project;
use Tests\AppBundle\Utils\ProjectTrait;
use Tests\AppBundle\Utils\PurgeTestCase;
use Tests\AppBundle\Utils\UserTrait;

/**
 * Class ProjectServiceTest
 * @package Tests\AppBundle\Service
 */
class ProjectServiceTest extends PurgeTestCase
{
    use UserTrait;
    use ProjectTrait;

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetProject ()
    {
        $user = $this->createUser();
        $this->save($user);
        $project = $this->createProject($user);
        $this->save($project);

        $service = new ProjectService($this->getEntityManager());
        $propro = $service->getProject($user, $project->getId());
        $this->assertNotNull($propro);
        $this->assertTrue($propro instanceof Project);
        $this->assertEquals($project->getName(), $propro->getName());
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetProjectWrongId ()
    {
        $user = $this->createUser();
        $this->save($user);
        $project = $this->createProject($user);
        $this->save($project);

        $service = new ProjectService($this->getEntityManager());
        $propro = $service->getProject($user, $project->getId() + 1);
        $this->assertNull($propro);
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetProjectWrongUser ()
    {
        $user = $this->createUser();
        $this->save($user);
        $user2 = $this->createUser([
            'firstname' => 'jean-paul',
            'email'     => 'jpdupont@gmail.com'
        ]);
        $this->save($user2);
        $project = $this->createProject($user);
        $this->save($project);

        $service = new ProjectService($this->getEntityManager());
        $propro = $service->getProject($user2, $project->getId());
        $this->assertNull($propro);
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGetProjectList ()
    {
        $user = $this->createUser();
        $this->save($user);
        $service = new ProjectService($this->getEntityManager());

        //0
        $list = $service->getProjectList($user);
        $this->assertNotNull($list);
        $this->assertEmpty($list);

        //1
        $project = $this->createProject($user);
        $this->save($project);
        $list = $service->getProjectList($user);
        $this->assertNotEmpty($list);
        $this->assertEquals(1, count($list));
        $this->assertEquals($project->getName(), $list[0]->getName());
    }
}
