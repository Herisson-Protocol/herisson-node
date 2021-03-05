<?php

namespace Herisson\Controller;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HerissonController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    protected $em;

    public function loadEntityManager()
    {
        //$this->em = $this->getDoctrine()->getManager();
    }

}
