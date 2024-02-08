<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints;

/**
 * Mayor o igual que
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class GreaterThanOrEqual extends AbstractComparison {

    public $message = 'This value should be greater than or equal to {{ compared_value }}.';
}
