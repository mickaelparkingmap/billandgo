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

use BillAndGoBundle\Entity\Project;
use BillAndGoBundle\Entity\User;

/**
 * Trait ProjectTrait
 * @package Tests\AppBundle\Utils
 */
trait ProjectTrait
{

    /**
     * @param User $user
     * @param array $data
     * @return Project
     */
    private function createProject (User $user, array $data = []) : Project
    {
        $project = new Project();
        $project->setRefUser($user);
        $project->setName('macron');
        if (isset($data['name'])) {
            $project->setName($data['name']);
        }
        $project->setDescription("C'est notre projeeeeeeeeet");
        $project->setBegin(new \DateTime());
        $project->setDeadline(new \DateTime());
        return $project;
    }
}