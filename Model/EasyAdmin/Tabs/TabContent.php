<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\EasyAdmin\Tabs;

/**
 * Contenido de tab
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class TabContent {
    
    private $id;
    
    /**
     * Tipo de contenido de tab
     * @var Tab::TAB_* 
     */
    private $type;
    private $options;
    private $title;
    private $icon;
    private $route;
    private $routeParameters;
    
    /**
     * Metadata of properties
     * @var array
     */
    private $fields;
    
    /**
     * ¿Activa?
     * @var booelan
     */
    private $active = false;

    public function __construct(array $options = []) {
        $this->setOptions($options);
        $this->id = uniqid("tc-");
        $this->fields = [];
    }
    
    /**
     * Opciones de la tab
     * @param array $options
     * @return \Pandco\Bundle\AppBundle\Model\Core\Tab\TabContent
     */
    public function setOptions(array $options = []) {
        $this->options = $options;
        return $this;
    }
    
    /**
     * Busca una opcion
     * @param type $name
     * @return type
     */
    public function getOption($name) {
        return $this->options[$name];
    }
    
    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    public function getFields() {
        return $this->fields;
    }
    
    public function getRoute() {
        return $this->route;
    }

    public function getRouteParameters() {
        return $this->routeParameters;
    }

    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }

    public function setRouteParameters(array $routeParameters) {
        $this->routeParameters = $routeParameters;
        return $this;
    }
    
    public function getIcon() {
        return $this->icon;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function addField($field,array $metadata) {
        $this->fields[$field] = $metadata;
        return $this;
    }
    
    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }
        
    /**
     * Representacion de la tab en arary
     * @return array
     */
    public function toArray() {
        $data = [
            "id" => $this->id,
            "options" => $this->options,
            "title" => $this->title,
        ];
        return $data;
    }
    
    public static function createFromMetadata(array $metadata) {
        $instance = new self();
        
        $instance->setTitle($metadata["title"]);
        $instance->setType($metadata["type"]);
        if(isset($metadata["icon"])){
            $instance->setIcon($metadata["icon"]);
        }
        
        if(isset($metadata["route"])){
            $routeParameters = isset($metadata["route_parameters"]) ? $metadata["route_parameters"] : [];
            $instance
                    ->setRoute($metadata["route"])
                    ->setRouteParameters($routeParameters)
                    ;
        }
        
        return $instance;
    }
}
