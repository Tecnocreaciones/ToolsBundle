<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UserPassword extends Constraint
{
    public $message = 'The password can not be the same as before.';
    public $service = 'tecno.validator.user_password_not_equals';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return $this->service;
    }
}
