<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Block;

/**
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
interface DefinitionBlockWidgetBoxInterface 
{
    /**
     * Nombre del servicio definido
     */
    function getType();
    
    /**
     * Nombre de los posibles contenidos a renderizar y su descripcion asociado key=>value
     */
    function getNames();
    
    /**
     * Plantillas disponibles para renderizar el widget
     */
    function getTemplates();
    
    /**
     * Descripcion general del widget
     */
    function getDescription();
    
    /**
     * Dominio de traduccion que se usara
     */
    function getTranslationDomain();
    
    /**
     * Renderiza el widget solo si el usuario tiene permiso de verlo
     */
    function hasPermission($name = null);
    
    /**
     * Transforma los eventos su forma parseada
     */
    function getParseEvents();
    
    public function isNew($name);
    
    public function getInfo($name,$key,$default = null);
    
    /**
     * Cuenta cuantos widgets son nuevos en base a la fecha de la creacion
     */
    public function countNews();
    
    /**
     * Retorna el grupo al cual pertenece el widget
     */
    public function getGroup();
    
    /**
     * Filtros por defecto a añadir automaticamente en caso de tener cero widgets.
     */
    public function getDefaults();
    
}
