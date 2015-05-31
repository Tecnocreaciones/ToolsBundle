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
 * Generate sequences from fields entities database with doctrine and a mask
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class SequenceGenerator implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    
    /**
     * Mode generates the next sequence
     */
    const MODE_NEXT = 0;
    /**
     * Mode generates the last sequence
     */
    const MODE_LAST = 1;
    
    /**
     * Options of sequence generator
     * @var array
     */
    private $options = array();
    
    private $cacheMemorySequences;
    private $cacheMemoryMasks;
            
    function __construct(array $options = array())
    {
        $this->setOptions($options);
        $this->cacheMemorySequences = array();
        $this->cacheMemoryMasks = array();
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * additional_masks:  Additional user defined masks (array empty by default)
     *   * debug:             Whether to enable debugging or not (false by default)
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        $this->options = array(
            'additional_masks'  => array(),
            'debug'             => false,
            'temporary_mask'    => 'TEMP',
        );

        // check option names and live merge, if errors are encountered Exception will be thrown
        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The SequenceGenerator does not support the following options: "%s".', implode('", "', $invalid)));
        }
    }
    
    /**
     * Generated based on the sequence parameters
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param type $mask
     * @param string $field
     * @param type $mode
     * @param type $parameters
     * @return type
     * @throws \InvalidArgumentException
     */
    protected function generate(\Doctrine\ORM\QueryBuilder $qb, $mask,$field,$mode = self::MODE_NEXT,$parameters = array()) 
    {
        $aliases = $qb->getRootAliases();
        $alias = $aliases[0];
        $field = $alias.'.'.$field;
        
        if($mode === null){
            $mode = self::MODE_NEXT;
        }
        
        if (!preg_match('/\{(0+)([@\+][0-9]+)?([\-][0-9]+)?\}/i', $mask, $reg))
            throw new \InvalidArgumentException('Incorrect format mask, the counter is required "{00},{00+n},{00-n}"');
        
        $masktri = (isset($reg[1]) ? $reg[1] : '') . (isset($reg[2]) ? $reg[2] : '') . (isset($reg[3]) ? $reg[3] : '');
        $maskcounter = $reg[1];
        $maskOffSetAdd = $maskOffSetSubtract = 0;
        if (strlen($maskcounter) < 2)
            throw new \InvalidArgumentException('The sequence of the mask must not be less than 2 digits "{00}"');
        

        $masktype_value = "";
        $maskwithnocode = $mask;
        foreach ($this->getDefaultMasks() as $key => $value) {
            $maskwithnocode = preg_replace('/\{'.$key.'\}/i', $key, $maskwithnocode);
        }
        
        // If an offset is asked
        if (!empty($reg[2])){
            if (preg_match('/^\+/', $reg[2])){
                $maskOffSetAdd = preg_replace('/^\+/', '', $reg[2]);
            }
            if (preg_match('/^\-/', $reg[2])){
                $maskOffSetSubtract = preg_replace('/^\-/', '', $reg[2]);
            }
        }
        if (!empty($reg[3])){
            if (preg_match('/^\+/', $reg[3])){
                $maskOffSetAdd = preg_replace('/^\+/', '', $reg[3]);
            }
            if (preg_match('/^\-/', $reg[3])){
                $maskOffSetSubtract = preg_replace('/^\-/', '', $reg[3]);
            }
        }
        //Se remplaza el valor de las mascaras adicionales para obtener la longitud correcta
        foreach ($this->getAdditionalMasks() as $key => $value) {
            if(isset($parameters[$value])){
                $maskwithnocode = preg_replace('/\{'.$value.'\}/i',$parameters[$value], $maskwithnocode);
            }
        }
        
        $posnumstart = strpos($maskwithnocode, $maskcounter); // Pos of counter in final string (from 0 to ...)
        $sqlstring = 'SUBSTRING(' .$field . ', ' . ($posnumstart) . ', ' . strlen($maskcounter) . ')';
        $maskLike = trim($mask);
        $maskLike = str_replace("%", "_", $maskLike);
        // Replace protected special codes with matching number of _ as wild card caracter
        foreach ($this->getDefaultMasks() as $key => $value) {
            $maskLike = preg_replace('/\{'.$key.'\}/i', str_pad('',strlen($key),'_'), $maskLike);
        }
        foreach ($this->getAdditionalMasks() as $key => $value) {
            if(isset($parameters[$value])){
                $maskLike = preg_replace('/\{'.$value.'\}/i',$parameters[$value], $maskLike);
            }
        }
        $maskLike = str_replace($this->dol_string_nospecial('{' . $masktri . '}'), str_pad("", strlen($maskcounter), "_"), $maskLike);
        
        // Get counter in database
        $counter = 0;
        $qb->select('MAX('.$sqlstring.') as v')
                ;
        $qb->andWhere($qb->expr()->like($field, "'".$maskLike."'"));
        if(!preg_match('/'.$this->getTemporaryMask().'/', $maskLike)){
            $qb->andWhere($qb->expr()->notLike($field, "'%".$this->getTemporaryMask()."%'"));
        }
        $result = $qb->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        if ($result) {
            $counter = $result['v'];
        }
        if (empty($counter) || preg_match('/[^0-9]/i', $counter))
            $counter = $maskOffSetAdd;
        $maskMd5 = md5($mask);
        if(!isset($this->cacheMemoryMasks[$maskMd5])){
            $this->cacheMemoryMasks[$maskMd5] = 0;
        }else{
            $this->cacheMemoryMasks[$maskMd5] = (integer)$this->cacheMemoryMasks[$maskMd5] + 1;
        }
        $counter+=$maskOffSetAdd;
        $counter-=$maskOffSetSubtract;
        
        if ($mode == self::MODE_NEXT) {
            //Incrementar el contador por secuencia que aun no se guarda
            $counter += (integer)$this->cacheMemoryMasks[$maskMd5];
            $counter++;
        }
        // Build numFinal
        $numFinal = $mask;
        $date = new \DateTime();
        // We replace special codes except refclient
        foreach ($this->getDefaultMasks() as $key => $value) {
            $numFinal = preg_replace('/\{'.$key.'\}/i',$date->format($value['date_format']), $numFinal);
        }
        
        foreach ($this->getAdditionalMasks() as $additionalMask) {
            if(isset($parameters[$additionalMask])){
                $numFinal = preg_replace('/\{'.$additionalMask.'\}/i', $parameters[$additionalMask], $numFinal);
            }
        }
        // Now we replace the counter
        $maskbefore = '{' . $masktri . '}';
        if($counter < 0){
            throw new \InvalidArgumentException('The sequence can not be negative, please check the rest in the mask. Result of sequence counter is "'.$counter.'"');
        }
        $maskafter = str_pad($counter, strlen($maskcounter), "0", STR_PAD_LEFT);
        $numFinal = str_replace($maskbefore, $maskafter, $numFinal);
        $this->cacheMemorySequences[] = $numFinal;
        return $numFinal;
    }
    
    /**
     * Generates the next sequence according to the parameters
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param type $mask Mask sequence to build for example "Example-{dd}-{mm}-{yy}-{yyyy}-{000})"
     * @param type $field Field to consult the entity
     * @param type $parameters Values of additional masks array('miMask' => 'Value')
     * @return type
     */
    function generateLast(\Doctrine\ORM\QueryBuilder $qb,$mask,$field,$parameters = array()) {
        return $this->generate($qb, $mask,$field,self::MODE_LAST,$parameters);
    }
    
    /**
     * Generates the next sequence according to the parameters
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param type $mask Mask sequence to build for example "Example-{dd}-{mm}-{yy}-{yyyy}-{000})"
     * @param type $field Field to consult the entity
     * @param type $parameters Values of additional masks array('miMask' => 'Value')
     * @return type
     */
    function generateNext(\Doctrine\ORM\QueryBuilder $qb,$mask,$field,$parameters = array())
    {
        return $this->generate($qb, $mask,$field,self::MODE_NEXT,$parameters);
    }
    
    /**
     * Generates the last sequence temporaly according to the parameters
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param type $field Field to consult the entity
     * @return type
     */
    function generateNextTemp(\Doctrine\ORM\QueryBuilder $qb,$field)
    {
        $temporaryMask = $this->getTemporaryMask().'-{000}';
        return $this->generate($qb,$temporaryMask ,$field,self::MODE_NEXT);
    }
    
    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    protected function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
         $this->container = $container;
    }
    
    /**
     * Returns a doctrine query builder
     * @param type $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public final function createQueryBuilder($alias = 'q') {
         return $this->getDoctrine()->getManager()->createQueryBuilder($alias);
    }
    
    private function dol_string_nospecial($str,$newstr='_',$badchars='')
    {
            $forbidden_chars_to_replace=array(" ","'","/","\\",":","*","?","\"","<",">","|","[","]",",",";","=");
            $forbidden_chars_to_remove=array();
            if (is_array($badchars)) $forbidden_chars_to_replace=$badchars;
            return str_replace($forbidden_chars_to_replace,$newstr,str_replace($forbidden_chars_to_remove,"",$str));
    }
    
    /**
     * Returns the masks defined by default
     * @return array
     */
    private function getDefaultMasks() {
        return array(
            'yyyy' => array('date_format' => 'Y'),
            'yy' => array('date_format' => 'y'),
            'mm' => array('date_format' => 'm'),
            'dd' => array('date_format' => 'd'),
        );
    }
    
    /**
     * Returns additional masks user added
     * @return array
     */
    public final function getAdditionalMasks() {
        return $this->options['additional_masks'];
    }
 
    /**
     * Valid mask
     * @param type $mask
     * @return type
     */
    public function isValidMask($mask) {
        return preg_match('/\{(0+)([@\+][0-9]+)?([\-][0-9]+)?\}/i',$mask);
    }
    
    public function getTemporaryMask()
    {
        return $this->options['temporary_mask'];
    }
}