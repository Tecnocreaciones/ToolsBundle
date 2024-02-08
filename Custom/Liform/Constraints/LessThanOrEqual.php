<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints;

/**
 * Menos o igual que
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class LessThanOrEqual extends AbstractComparison {
    
    public $message = 'This value should be less than or equal to {{ compared_value }}.';
}
