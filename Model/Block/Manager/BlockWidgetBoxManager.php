<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Block\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox;

/**
 * Description of BlockWidgetBoxManager
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class BlockWidgetBoxManager implements BlockWidgetBoxManagerInterface,  ContainerAwareInterface
{
    /**
     *
     * @var BlockWidgetBox
     */
    protected $classBox;
    
    protected $container;
            
    function __construct($classBox) {
        $this->classBox = $classBox;
    }
    
    /**
     * 
     * @return BlockWidgetBox
     */
    public function createNew() {
        return new $this->classBox;
    }

    public function buildBlockWidget(array $parameters = array()) {
        $blockWidget = $this->createNew();
        return $blockWidget;
    }
    
    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }
    
    /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

}
