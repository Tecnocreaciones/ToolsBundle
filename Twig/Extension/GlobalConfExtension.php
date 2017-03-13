<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

/**
 * Extension de twig que provee herramientas globales
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class GlobalConfExtension extends \Twig_Extension implements \Symfony\Component\DependencyInjection\ContainerAwareInterface 
{

    private $container;
    
    /**
     * @return \Tecnoready\Common\Service\ConfigurationService\ConfigurationManager
     */
    protected function getConfigurationManager() {
        return $this->container->get($this->container->getParameter('tecnocreaciones_tools.configuration_manager.name'));
    }
    
    public function getGlobals() {
        return array('appConfiguration' => $this->getConfigurationManager());
    }
    
    public function getFunctions() 
    {
        $functions[] = new \Twig_SimpleFunction('getAppConfig', array($this, 'getAppConfig'));
        return $functions;
    }

    public function getName() 
    {
        return 'tecnocreaciones_tools_global_conf_extension';
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('clearString', array($this, 'clearString')),
        );
    }
    
    public function getAppConfig($method,$wrapperName) {
        $configurationManager = $this->getConfigurationManager();
        $wrapper = $configurationManager->getWrapper($wrapperName);
        
        return call_user_func_array(array($wrapper,$method),array());
    }
    
    /**
     * Reemplaza todos los acentos por sus equivalentes sin ellos
     *
     * @param $string
     *  string la cadena a sanear
     *
     * @return $string
     *  string saneada
     */
    function clearString($string)
    {

        $string = trim($string);

        $string = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
                array("\\", "¨", "º", "-", "~",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "`", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":",
            ".", " "), '', $string
        );

        return $string;
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }

}
