<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\HttpGuzzle;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Tecnocreaciones\Bundle\ToolsBundle\Service\HttpGuzzle\Handler\NativeHandler;
use GuzzleHttp\Psr7\Response;

/**
 * Cliente guzzle para peticiones http
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ClientGuzzle extends BaseClientGuzzle
{
    public function request($method,$uri,array $options = []){
        $this->lastRequest = $this->request;
        $options["method"] = $method;
        $options["uri"] = $uri;
        $options = array_merge($this->options,$options);
        
        $body = $this->doRequest($this->request,$uri,$options);

        $response = null;
        if ($this->isSuccess() && !empty($body)) {
            $response = $body;
        }else{
//            $response = new Response(406);
        }
        
        return $response;
    }
    
    protected function configureOptions(OptionsResolver $resolver)
    {
        $nativeHandler = new NativeHandler();
        $resolver->setDefaults([
            "default_handler" => $nativeHandler,
            "form_params" => null,
        ]);
    }
    
    /**
     * Retorna el ultimo cuerpo de la peticion realizada
     * @return string|null
     */
    public function getLastBody()
    {
        return $this->handler->getLastBody();
    }
}
