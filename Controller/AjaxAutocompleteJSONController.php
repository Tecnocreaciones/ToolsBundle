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
        $repositoryMethod = $entityInf['repository_method'];
        $formTest = $this->createForm($entityInf['form']);
        $field = $request->get("field");
        
        
        $child = $formTest->get($field);
        
        $className = $entityInf['class'];
        
        $childConfig = $child->getConfig();
        $attributes = $childConfig->getAttributes();
        $property = $attributes['property'];
        
        $repository = $em->getRepository($className);
        $queryBuilder = $repository->createQueryBuilder("a");
        
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
        if($repositoryMethod !== null){
            $qb = $repository->$repositoryMethod();
            if($qb === null || !($qb instanceof \Doctrine\ORM\QueryBuilder) ){
                throw new \RuntimeException(sprintf("The repository method '%s' must be return a 'Doctrine\ORM\QueryBuilder' instance."));
            }
            $queryBuilder = $qb;
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
//            ->setMaxResults($maxRows)
            ;
        $paginator = new \Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator\Paginator(new \Pagerfanta\Adapter\DoctrineORMAdapter($queryBuilder));
        $paginator
                ->setMaxPerPage($maxRows)
                ;
        $results = $paginator->getCurrentPageResults();
        $more = false;
        if($paginator->hasNextPage()){
            $more = true;
        }

        $items = array();
        foreach ($results AS $entity){
            $items[] = array(
                'id'    => $entity->getId(),
                'text' => (string)$entity,
            );
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'items'  => $items,
            'total_count'  => $paginator->getNbResults(),
            'incomplete_results' => false,
            'more' => $more,
        ));

    }
}
