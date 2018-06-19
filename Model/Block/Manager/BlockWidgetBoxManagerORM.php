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

/**
 * Manejador de widgets ORM
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class BlockWidgetBoxManagerORM extends BlockWidgetBoxManager
{
    function find($id)
    {
        $blockWidgetBox = $this->getRepository()->find($id);
        return $blockWidgetBox;
    }
    
    function findByIds(array $ids)
    {
        $qb = $this->getRepository()->createQueryBuilder('b');
        $qb
            ->andWhere($qb->expr()->in('b.id',$ids))
            ;
        return $qb->getQuery()->getResult();
    }
    
    public function findAllPublishedByEvent($event)
    {
        $user = $this->getUser();
        return $this->getRepository()->findBy(array(
            'event' => $event,
            'user' => $user,
            'enabled' => true
        ));
    }
    
    public function remove(\Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox $blockWidgetBox) 
    {
        $success = false;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($blockWidgetBox->getUser() === $user){
            $em->remove($blockWidgetBox);
            $em->flush();
            $success = true;
        }
        return $success;
    }

    public function save(\Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox $blockWidgetBox,$andFlush = true)
    {
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($blockWidgetBox);
        if($andFlush){
            $em->flush();
        }
        
        return $blockWidgetBox;
    }
    
    public function buildBlockWidget(array $parameters = array()) {
        $user = $this->getUser();
        
        $blockWidget = parent::buildBlockWidget($parameters);
        $blockWidget->setUser($user);
        
        return $blockWidget;
    }
    
    private function getRepository()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository($this->classBox);
        return $repository;
    }

    public function countPublishedByEvent($event) {
        $user = $this->getUser();
        $qb = $this->getRepository()->createQueryBuilder("w");
        
        $qb->select("COUNT(w.id) total")
          ->andWhere("w.event = :event")
          ->andWhere("w.user = :user")
          ->andWhere("w.enabled = :enabled")
          ->setParameter("event",$event)
          ->setParameter("user",$user)
          ->setParameter("enabled",true)
                ;
        $result = $qb->getQuery()->getOneOrNullResult();
        return (int)$result["total"];
    }
}
