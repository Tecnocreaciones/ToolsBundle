<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

/**
 * Manejador de seguridad
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class SecurityHandler implements ContainerAwareInterface
{
    private $container;
    protected $genericMessage = 'pequiven_seip.security.permission_denied';
    protected $prefixMessage = 'pequiven_seip.security';


    protected function getMethodValidMap()
    {
        return array();
    }
    
    /**
     * Evalua que el usuario tenga acceso a la seccion especifica, ademas se valida con un segundo metodo
     * @param type $rol
     * @param type $parameters
     * @throws type
     */
    public function checkSecurity($rol,$parameters = null) {
        if($rol === null){
            throw $this->createAccessDeniedHttpException($this->trans($this->genericMessage));
        }
        $roles = $rol;
        if(!is_array($rol)){
            $roles = array($rol);
        }
        $valid = $this->getSecurityContext()->isGranted($roles,$parameters);
        foreach ($roles as $rol) {
            if(!$valid){
                throw $this->createAccessDeniedHttpException($this->buildMessage($rol));
            }
            $methodValidMap = $this->getMethodValidMap();
            if(isset($methodValidMap[$rol])){
                $method = $methodValidMap[$rol];
                $valid = call_user_func_array(array($this,$method),array($rol,$parameters));
            }
        }
    }
    
    /**
     * Genera el mensaje de error
     * @param type $rol
     * @param type $prefix
     * @return type
     */
    private function buildMessage($rol,$prefix = '403')
    {
        return $this->trans(sprintf('%s.%s.%s',  $this->prefixMessage,$prefix,strtolower($rol)));
    }

    /**
     * Returns a AccessDeniedHttpException.
     *
     * This will result in a 403 response code. Usage example:
     *
     *     throw $this->createAccessDeniedHttpException('Permission Denied!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    private function createAccessDeniedHttpException($message = 'Permission Denied!', \Exception $previous = null)
    {
        $this->setFlash('error', $message);
        return new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($message, $previous);
    }
    
    protected function trans($id,array $parameters = array(), $domain = 'messages')
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
    
    /**
     * Envia un mensaje flash
     * 
     * @param array $type success|error
     * @param type $message
     * @param type $parameters
     * @param type $domain
     * @return type
     */
    protected function setFlash($type,$message,$parameters = array(),$domain = 'flashes')
    {
        return $this->container->get('session')->getBag('flashes')->add($type,$message);
    }
    
    /**
     * 
     * @return \Symfony\Component\Security\Core\SecurityContextInterface
     * @throws \LogicException
     */
    protected function getSecurityContext()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        return $this->container->get('security.context');
    }
    
     /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    private function getUser()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
    
    public function setContainer(ContainerInterface $container = null) 
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function createObjectSecurity(AdminInterface $admin, $object)
    {
        // retrieving the ACL for the object identity
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $acl            = $this->getObjectAcl($objectIdentity);
        if (is_null($acl)) {
            $acl = $this->createAcl($objectIdentity);
        }

        // retrieving the security identity of the currently logged-in user
        $user             = $this->securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $this->addObjectOwner($acl, $securityIdentity);
        $this->addObjectClassAces($acl, $this->buildSecurityInformation($admin));
        $this->updateAcl($acl);
    }
}
