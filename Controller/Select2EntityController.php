<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator\Paginator;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Controaldor para devolver data para select2 entity
 * @see Tetranz\Select2EntityBundle\Form\Type\Select2EntityType
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Select2EntityController extends AbstractFOSRestController
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    
    public function emailComponentAction(Request $request)
    {
        $view = $this->view();
        $typeComponent = $request->get("type");
        $query = $request->get("q");
        if(empty($typeComponent)){
            $this->createNotFoundException("El tipo de componente no puede estar vacio.");
        }
        
        $classComponent = $this->parameterBag->get("tecnoready.mailer_db.email_component_class");
        
        $alias = "e";
        $em = $this->getDoctrine()->getManagerForClass($classComponent);
        $repository = $em->getRepository($classComponent);
        $qb = $repository->createQueryBuilder($alias);
        $qb->andWhere("e.typeComponent = :typeComponent")
                ->setParameter("typeComponent",$typeComponent)
                ;
        $criteria = new ArrayCollection([
            "query" => $query,
        ]);
        $sqb = new SearchQueryBuilder($qb, $criteria, $alias);
        $sqb->addQueryField("query",["body","title"]);
        
        $paginator = new Paginator(new QueryAdapter($qb));
        $view->setData($this->buildData($request, $paginator));
        return $this->handleView($view);
    }
    
    /**
     * Construye el contenido del select2 ajax
     * @param Request $request
     * @param Paginator $paginator
     * @param type $toStringCallback
     * @return type
     */
    private function buildData(Request $request,Paginator $paginator,$toStringCallback = null)
    {
        $page = (int)$request->get("page");
        if($page <= 0){
            $page = 1;
        }
        $paginator->setMaxPerPage(5);
        $paginator->setCurrentPage($page);
        
        $data = [
            "results" => [],
            "more" => $paginator->hasNextPage(),
            "nb_results" => $paginator->getNbResults(),
        ];
        $results = $paginator->getCurrentPageResults();
        foreach ($results as $result) {
            $item = [
                "id" => $result->getId(),
            ];
            $item["text"] = $toStringCallback === null ? (string)$result : $toStringCallback($result);
            $data["results"][] = $item;
        }
        return $data;
    }
    
    /**
     * Se pasa null para darle compatibilidad con sf 3.x que no existe el servicio
     * @required
     * @param ParameterBagInterface $parameterBag
     * @return $this
     */
    public function setParameterBag(ParameterBagInterface $parameterBag = null) {
        $this->parameterBag = $parameterBag;
        return $this;
    }
}
