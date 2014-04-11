<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

/**
 * Description of SequenceGenerator
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class SequenceGenerator implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    const MODE_NEXT = 0;
    const MODE_LAST = 1;
    
    private $container;
    
    function generateLast(\Doctrine\ORM\QueryBuilder $qb,$field) {
        
    }
    
    function generate(\Doctrine\ORM\QueryBuilder $qb, $mask,$field,$mode = self::MODE_NEXT,$parameters = array()) 
    {
        $aliases = $qb->getRootAliases();
        $alias = $aliases[0];
        $field = $alias.'.'.$field;
        if($mode === null){
            $mode = self::MODE_NEXT;
        }
            
        // Extract value for mask counter, mask raz and mask offset
        if (!preg_match('/\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}/i', $mask, $reg))
            return 'ErrorBadMask';
        $masktri = (isset($reg[1]) ? $reg[1] : '') . (isset($reg[2]) ? $reg[2] : '') . (isset($reg[3]) ? $reg[3] : '');
        $maskcounter = $reg[1];
        $maskraz = -1;
        $maskoffset = 0;
        if (strlen($maskcounter) < 2)
            return 'CounterMustHaveMoreThan3Digits';
        
        $maskrefclient_maskcounter = '';
        $maskrefclient = '';

        $masktype_value = "";
            $masktype = '';

        $maskwithonlyymcode = $mask;
        $maskwithonlyymcode = preg_replace('/\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}/i', $maskcounter, $maskwithonlyymcode);
        $maskwithonlyymcode = preg_replace('/\{dd\}/i', 'dd', $maskwithonlyymcode);
        $maskwithonlyymcode = preg_replace('/\{(c+)(0*)\}/i', $maskrefclient, $maskwithonlyymcode);
        $maskwithonlyymcode = preg_replace('/\{(t+)\}/i', $masktype_value, $maskwithonlyymcode);
        $maskwithnocode = $maskwithonlyymcode;
        $defaultMask = new \Doctrine\Common\Collections\ArrayCollection(array(
            'yyyy','yy','mm'
        ));
        $maskwithnocode = preg_replace('/\{yyyy\}/i', 'yyyy', $maskwithnocode);
        $maskwithnocode = preg_replace('/\{yy\}/i', 'yy', $maskwithnocode);
        $maskwithnocode = preg_replace('/\{mm\}/i', 'mm', $maskwithnocode);
        // If an offset is asked
        if (!empty($reg[2]) && preg_match('/^\+/', $reg[2]))
            $maskoffset = preg_replace('/^\+/', '', $reg[2]);
        if (!empty($reg[3]) && preg_match('/^\+/', $reg[3]))
            $maskoffset = preg_replace('/^\+/', '', $reg[3]);
        
        $posnumstart = strpos($maskwithnocode, $maskcounter); // Pos of counter in final string (from 0 to ...)
        $sqlstring = 'SUBSTRING(' .$field . ', ' . ($posnumstart + 1) . ', ' . strlen($maskcounter) . ')';
        $maskLike = trim($mask);
        $maskLike = str_replace("%", "_", $maskLike);
        // Replace protected special codes with matching number of _ as wild card caracter
        $maskLike = preg_replace('/\{yyyy\}/i', '____', $maskLike);
        $maskLike = preg_replace('/\{yy\}/i', '__', $maskLike);
        $maskLike = preg_replace('/\{mm\}/i', '__', $maskLike);
        $maskLike = preg_replace('/\{dd\}/i', '__', $maskLike);
        $maskLike = str_replace($this->dol_string_nospecial('{' . $masktri . '}'), str_pad("", strlen($maskcounter), "_"), $maskLike);
        
        // Get counter in database
        $counter = 0;
        $qb->select('MAX('.$sqlstring.') as v')
                ;
        $qb->where($qb->expr()->like($field, "'".$maskLike."'"));
        $qb->andWhere($qb->expr()->notLike($field, "'%PROV%'"));
        
        $result = $qb->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        if ($result) {
            $counter = $result['v'];
        }
        if (empty($counter) || preg_match('/[^0-9]/i', $counter))
            $counter = $maskoffset;
        //$counter+=$maskoffset;
        
        if ($mode == self::MODE_NEXT) {
            $counter++;
        }
        // Build numFinal
        $numFinal = $mask;
        $date = new \DateTime();
        // We replace special codes except refclient
        $numFinal = preg_replace('/\{yyyy\}/i',$date->format("Y"), $numFinal);
        $numFinal = preg_replace('/\{yy\}/i', $date->format("y"), $numFinal);
        $numFinal = preg_replace('/\{mm\}/i', $date->format("m"), $numFinal);
        $numFinal = preg_replace('/\{dd\}/i', $date->format("d"), $numFinal);
        
        foreach ($this->getAdditionalMasks() as $additionalMask) {
            if(isset($parameters[$additionalMask])){
                $numFinal = preg_replace('/\{'.$additionalMask.'\}/i', $parameters[$additionalMask], $numFinal);
            }
        }
        // Now we replace the counter
        $maskbefore = '{' . $masktri . '}';
        $maskafter = str_pad($counter, strlen($maskcounter), "0", STR_PAD_LEFT);
        $numFinal = str_replace($maskbefore, $maskafter, $numFinal);
        
        return $numFinal;
    }
    
    function generateNext(\Doctrine\ORM\QueryBuilder $qb,$mask,$field,$parameters = array()) {
        return $this->generate($qb, $mask,$field,self::MODE_NEXT,$parameters);
    }
    
    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
         $this->container = $container;
    }
    
    /**
     * 
     * @param type $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public final function createQueryBuilder($alias = 'q') {
         return $this->getDoctrine()->getManager()->createQueryBuilder($alias);
    }
    
    function dol_string_nospecial($str,$newstr='_',$badchars='')
    {
            $forbidden_chars_to_replace=array(" ","'","/","\\",":","*","?","\"","<",">","|","[","]",",",";","=");
            $forbidden_chars_to_remove=array();
            if (is_array($badchars)) $forbidden_chars_to_replace=$badchars;
            //$forbidden_chars_to_remove=array("(",")");

            return str_replace($forbidden_chars_to_replace,$newstr,str_replace($forbidden_chars_to_remove,"",$str));
    }
    
    function getAdditionalMasks() {
        return array('CAT','ZONE');
    }
}