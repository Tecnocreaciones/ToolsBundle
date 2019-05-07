<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Email;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Formulario para listar entidades de cada tipo
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ComponentEntityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired("type_component");
        $resolver->setDefault("placeholder","email.component.empty.select");
        
        $queryBuilderNormalizer = function (Options $options, $queryBuilder) {
            $repository = $options['em']->getRepository($options['class']);
            $queryBuilder = $repository->createQueryBuilder("c");
            $queryBuilder
                ->andWhere("c.typeComponent = :typeComponent")
                ->setParameter("typeComponent",$options["type_component"])
                ;

            return $queryBuilder;
        };

        $resolver->setNormalizer('query_builder', $queryBuilderNormalizer);
    }
    public function getParent()
    {
        return "Symfony\Bridge\Doctrine\Form\Type\EntityType";
    }
}
