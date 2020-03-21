<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base de test
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class BaseWebTestCase extends WebTestCase
{

    private $client;

    public function setUp(): void
    {
        parent::setUp();
    }

    private function getMyClient()
    {
        if($this->client === null){
            $this->client = static::createClient();
        }
        return $this->client;
    }

    protected function getContainer()
    {
        return $this->getMyClient()->getContainer();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get("doctrine");
    }

    /**
     * Retorna un servicio del contenedor
     * @param type $id
     * @return type
     */
    protected function get($id)
    {
//        return $this->getMyClient()->getKernel()->getContainer()->get($id);
        return $this->getContainer()->get($id);
    }

}
