<?php
namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

use Zend\Json\Json;
use Zend\Json\Expr;

use Assetic\AssetManager;
use Assetic\Asset\FileAsset;

class CodeMirrorExtension extends \Twig_Extension
{
    /**
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Asset\AssetManager
     */
    protected $assetManager;

    function __construct($assetManager)
    {
        $this->assetManager = $assetManager;
    }

    protected $isFirstCall = true;

    public function getFunctions()
    {
        return array(
            'code_mirror_parameters_render' => new \Twig_Function_Method($this, 'parametersRender', array('is_safe' => array('html'))),
            'code_mirror_is_first_call' => new \Twig_Function_Method($this, 'isFirstCall'),
            'code_mirror_get_js_mode' => new \Twig_Function_Method($this, 'code_mirror_get_js_mode'),
            'code_mirror_get_css_theme' => new \Twig_Function_Method($this, 'code_mirror_get_css_theme'),
            'code_mirror_get_lib' => new \Twig_Function_Method($this, 'code_mirror_get_lib'),
        );
    }

    public function parametersRender($paramters)
    {
        $params = $paramters;
        if (isset($paramters['mode'])) {
            $params['mode'] = new Expr('"' . $paramters['mode'] . '"');
        }
      
        $params = Json::encode($params, false, array('enableJsonExprFinder' => true));

        $this->isFirstCall = false;

        return $params;
    }

    public function code_mirror_get_js_mode($parameters)
    {
        $result = $this->assetManager->getAddonsParsed();
        $result = array_merge($result, $this->assetManager->getExtraModes());
        $result = array_merge($result, (array)$this->assetManager->getMode($parameters['mode']));
        return $result;
    }

    public function code_mirror_get_lib()
    {
        return $this->assetManager->getCodemirrorLib();
    }

    public function code_mirror_get_css_theme($parameters)
    {
        if(!isset($parameters['theme'])){
            return;
        }
        $am = new AssetManager();
        $am->set('theme', new FileAsset($parameters['theme']));
        $am->get('theme');

        #var_dump($am, $am->get('theme'), $am->getNames()); die;

        if(isset($parameters['theme']) AND $theme = $this->assetManager->getTheme($parameters['theme'])) {
            return $theme;
        }
        return false;
    }

    public function isFirstCall()
    {
        return $this->isFirstCall;
    }

    public function getName()
    {
        return 'code_mirror_extension';
    }
}
