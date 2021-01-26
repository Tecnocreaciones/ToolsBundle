<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Campo de archivo
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicFileType extends FileType
{
    const MODE_FILE = "FILE";
    const MODE_IMAGEN = "IMAGEN";
    
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired(["mode"]);
    }
}
