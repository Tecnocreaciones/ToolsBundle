<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Response;

class AjaxAutocompleteJSONController extends Controller
{
    public function getJSONAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entities = $this->get('service_container')->getParameter('tecnocreaciones.extra_form_types.autocomplete_entities');
        
        $entity_alias = $request->get('entity_alias');
        $entityInf = $entities[$entity_alias];
        
        $formTest = $this->createForm($entityInf['form']);
        
        
        $child = $formTest->get("products");
        
        $className = $entityInf['class'];
        
        $childConfig = $child->getConfig();
        $attributes = $childConfig->getAttributes();
        $property = $attributes['property'];
        
        $repository = $em->getRepository($className);
        $queryBuilder = null;
        
        $callback = null;
        if(isset($attributes['data_collector/passed_options'])){
            $passedOptions = $attributes['data_collector/passed_options'];
            if(isset($passedOptions['callback'])){
                $callback = $passedOptions['callback'];
            }
            if(isset($passedOptions['property'])){
                $property = $passedOptions['property'];
            }
        }
        
        if($callback === null && isset($attributes["callback"])){
            $callback = $attributes["callback"];
        }
        if($callback !== null){
            if(is_callable($callback)){
                $qb = $callback($repository);
                if($qb !== null && $qb instanceof \Doctrine\ORM\QueryBuilder ){
                    $queryBuilder = $qb;
                }
            }
        }
        

        if ($entityInf['role'] !== 'IS_AUTHENTICATED_ANONYMOUSLY'){
            if (false === $this->get('security.context')->isGranted( $entityInf['role'] )) {
                throw new AccessDeniedException();
            }
        }

        $letters = $request->get('q');
        $maxRows = $request->get('maxRows',20);

        switch ($entityInf['search']){
            case "begins_with":
                $like = $letters . '%';
            break;
            case "ends_with":
                $like = '%' . $letters;
            break;
            case "contains":
                $like = '%' . $letters . '%';
            break;
            default:
                throw new \Exception('Unexpected value of parameter "search"');
        }

//        if ($entityInf['case_insensitive']) {
//                $where_clause_lhs = 'WHERE LOWER(e.' . $property . ')';
//                $where_clause_rhs = 'LIKE LOWER(:like)';
//        } else {
//
//                $where_clause_lhs = 'WHERE e.' . $property;
//                $where_clause_rhs = 'LIKE :like';
//        }
        
        $alias = $queryBuilder->getRootAlias();
        if(is_array($property)){
            $orX = $queryBuilder->expr()->orX();
            foreach ($property as $p) {
                $orX->add($queryBuilder->expr()->like($alias.'.'.$p, "'".$like."'"));
                $property = $p;
            }
            $queryBuilder
                ->andWhere($orX);
        }else{
            $queryBuilder
                ->andWhere($queryBuilder->expr()->like($alias.'.'.$property, "'".$like."'"));
        }
        $queryBuilder
            ->orderBy($alias.".".$property)
            ->setMaxResults($maxRows)
            ;
        
        $results = $queryBuilder->getQuery()->getResult();
//            print_r($queryBuilder->getQuery()->getSQL());
//        $results = $em->createQuery(
//            'SELECT e.' . $property . '
//             FROM ' . $entityInf['class'] . ' e ' .
//             $where_clause_lhs . ' ' . $where_clause_rhs . ' ' .
//            'ORDER BY e.' . $property)
//            ->setParameter('like', $like )
//            ->setMaxResults($maxRows)
//            ->getScalarResult();

        $items = array();
        foreach ($results AS $entity){
//            $res[] = $r[$entityInf['property']];
            $items[] = array(
                'id'    => $entity->getId(),
                'label' => (string)$entity,
            );
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'status' => 'OK',
            'more'   => false,
            'items'  => $items
        ));

    }
}
