<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Evalua herencia de roles por prefijo
 * Ejemplo (ROLE_APP_EXAMPLE Coincide con el prefijo ROLE_APP_)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RolePatternVoter extends RoleVoter {

    private $rolePrefix;
    private $prefixLength;
    private $pattern;
    private $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy, $prefix = 'ROLE_APP_') {
        $this->roleHierarchy = $roleHierarchy;
        $this->rolePrefix = $prefix;
        $this->prefixLength = strlen($prefix);
        $this->pattern = sprintf('/^%s([A-Z])\w+\*/',$this->rolePrefix);
        parent::__construct($prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function extractRoles(TokenInterface $token) {
        return $this->roleHierarchy->getReachableRoles($token->getRoles());
    }
    
    public function vote(TokenInterface $token, $object, array $attributes) {
        $result = VoterInterface::ACCESS_ABSTAIN;
        $roles = $this->extractRoles($token);
        
        foreach ($attributes as $attribute) {
            if ($attribute === null || substr($attribute,0,$this->prefixLength) !== $this->rolePrefix) {
                continue;
            }
            if(preg_match($this->pattern, $attribute)){
                $result = VoterInterface::ACCESS_DENIED;
                foreach ($roles as $role) {
                    $attribute = str_replace('*', '', $attribute);
                    if(strpos($role->getRole(),$attribute) !== false){
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
            }
        }
        return $result;
    }

}
