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
    
    const CROP_IMAGEN_RECTANGLE = "RECTANGLE";
    const CROP_IMAGEN_OVAL = "OVAL";
    
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            "crop_imagen_mode" => null,
            "file_type" => null,
            "picker_title" => null,
//            "crop_imagen_mode" => self::CROP_IMAGEN_RECTANGLE,
        ]);
        $resolver->setRequired(["mode"]);
    }
}
