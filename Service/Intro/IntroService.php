<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Intro;

/**
 * Servicio de introduccion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class IntroService
{
    protected $adapters;
    
    protected $config;
    
    /**
     *
     * @var Twig
     */
    protected $templating;
    /**
     *
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;
    
    /**
     * 
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    protected $securityContext;
    
    public function __construct() {
        $this->adapters = array();
    }
    
    function addAdapter(Adapter\IntroAdapterInterface $adapter)
    {
        
    }
    
    public function renderArea($area)
    {
        $introClass = $this->config['intro_class'];
        $introLogClass = $this->config['intro_log_class'];
        $em = $this->doctrine->getManager();
        $user = $this->getUser();
        
        $intros = $em->getRepository($introClass)->findBy(array(
            'area' => $area,
            'enabled' => true,
        ));

        $introsById = array();
        foreach ($intros as $intro) {
            $introsById[$intro->getId()] = $intro;
        }
        $qb = $em->createQueryBuilder();
        $qb
            ->select('l')
            ->addSelect('l_i')
            ->from($introLogClass,'l')
            ->innerJoin('l.intro','l_i')
            ->andWhere('l.user = :user')
            ;
            $orX = $qb->expr()->orX();
            foreach ($introsById as $id => $intro) {
                $orX->add($qb->expr()->eq('l_i.id', $id));
            }
            if($orX->count() > 0){
                $qb
                    ->andWhere($orX);
            }
            $qb->setParameter('user',$user);
        $logs = $qb->getQuery()->getResult();
        foreach ($logs as $log) 
        {
            if($log->isRenderable() === false){
                unset($introsById[$log->getIntro()->getId()]);
            }
        }
        $template = 'TecnocreacionesToolsBundle:Intro:intro.js.twig';
//        $this->templating->setLoader(new \Twig_Loader_String());
        
        return $this->renderView($template,array(
            'intros' => $introsById,
            'intro_service' => $this,
        ));
    }
    
    /**
     * Genera un nuevo log
     * 
     * @param type $introId
     * @param type $user
     * @param type $typeLog
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Service\Intro\introLogClass
     */
    public function newLog($introId,$user)
    {
        $introClass = $this->config['intro_class'];
        $em = $this->doctrine->getManager();
        $intro = $em->getRepository($introClass)->find($introId);
        $introLog = null;
        if($intro){
            $introLogClass = $this->config['intro_log_class'];
            $introLog = new $introLogClass();
            $introLog->setIntro($intro);
            $introLog->setUser($user);
        }
        return $introLog;
    }
    
    public function log($introId,$user,$typeLog) 
    {
        $log = $this->findLog($introId, $user);
        if(!$log){
            $log = $this->newLog($introId, $user);
        }
        if($log){
            $log->autoLog($typeLog);
            $em = $this->doctrine->getManager();
            $em->persist($log);
            $em->flush();
        }
        return $log;
    }
    
    public function findLog($introId,$user)
    {
        $em = $this->doctrine->getManager();
        $introLogClass = $this->config['intro_log_class'];
        $log = $em->getRepository($introLogClass)->findOneBy(array(
            'user' => $user,
            'intro' => $introId,
        ));
        return $log;
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        return $this->templating->render($view, $parameters);
    }
    
    function getAreas() {
        static $areas = null;
        if($areas === null){
            $areas = array();
            foreach ($this->config['areas'] as $area) {
                $areas[$area] = $area;
            }
        }
        return $areas;
    }

    function setConfig(array $config) 
    {
        $this->config = $config;
    }
    
    function setTemplating($templating)
    {
        $this->templating = $templating;
    }
    
    function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine) {
        $this->doctrine = $doctrine;
    }
    
    function setSecurityContext(\Symfony\Component\Security\Core\SecurityContext $securityContext) {
        $this->securityContext = $securityContext;
    }
    
    /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    public function getUser()
    {
        if (null === $token = $this->securityContext->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
}
