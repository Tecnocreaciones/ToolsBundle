<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Doctrine\Common\Util\ClassUtils;

/**
 * Transform object to ID and property label
 *
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class ModelToIdPropertyTransformer implements DataTransformerInterface
{
    protected $modelManager;

    protected $className;

    protected $property;

    protected $multiple;

    protected $toStringCallback;

    /**
     * @param ModelManagerInterface $modelManager
     * @param string                $className
     * @param string                $property
     */
    public function __construct(\Tecnocreaciones\Bundle\ToolsBundle\Model\ModelManager $modelManager, $className, $property, $multiple=false, $toStringCallback=null)
    {
        $this->modelManager     = $modelManager;
        $this->className        = $className;
        $this->property         = $property;
        $this->multiple         = $multiple;
        $this->toStringCallback = $toStringCallback;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        $collection = $this->modelManager->getModelCollectionInstance($this->className);

        if (empty($value)) {
            if ($this->multiple) {
                return $collection;
            }

            return null;
        }

        if (!$this->multiple) {
             return $this->modelManager->find($this->className, $value);
        }

        if (!is_array($value)) {
            throw new \UnexpectedValueException(sprintf('Value should be array, %s given.', gettype($value)));
        }

        foreach ($value as $key => $id) {
            if ($key === '_labels') {
                continue;
            }
            if ($key === 'selected' && is_array($id)) {
                foreach ($id as $item) {
                    $model = $this->modelManager->find($this->className, $item['id']);
                    if(!$collection->contains($model)){
                        $collection->add($model);
                    }
                }
            }else{
                $model = $this->modelManager->find($this->className, $id);
                if(!$collection->contains($model)){
                    $collection->add($model);
                }
            }
        }
        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($entityOrCollection)
    {
        $result = array();

        if (!$entityOrCollection) {
            return $result;
        }

        if ($this->multiple) {
            if (substr(get_class($entityOrCollection), -1 * strlen($this->className)) == $this->className) {
                throw new \InvalidArgumentException('A multiple selection must be passed a collection not a single value. Make sure that form option "multiple=false" is set for many-to-one relation and "multiple=true" is set for many-to-many or one-to-many relations.');
            } elseif ($entityOrCollection instanceof \ArrayAccess) {
                $collection = $entityOrCollection;
            } else {
                throw new \InvalidArgumentException('A multiple selection must be passed a collection not a single value. Make sure that form option "multiple=false" is set for many-to-one relation and "multiple=true" is set for many-to-many or one-to-many relations.');
            }
        } else {
            if (substr(get_class($entityOrCollection), -1 * strlen($this->className)) == $this->className) {
                $collection = array($entityOrCollection);
            } elseif ($entityOrCollection instanceof \ArrayAccess) {
                throw new \InvalidArgumentException('A single selection must be passed a single value not a collection. Make sure that form option "multiple=false" is set for many-to-one relation and "multiple=true" is set for many-to-many or one-to-many relations.');
            } else {
                $collection = array($entityOrCollection);
            }
        }

        if ($this->toStringCallback !== null && empty($this->property)) {
            throw new \RuntimeException('Please define "property" parameter.');
        }

        $result['selected'] = [];
        foreach ($collection as $entity) {
            $id  = current($this->modelManager->getIdentifierValues($entity));

            if ($this->toStringCallback !== null) {
                if (!is_callable($this->toStringCallback)) {
                    throw new \RuntimeException('Callback in "to_string_callback" option doesn`t contain callable function.');
                }

                $label = call_user_func($this->toStringCallback, $entity, $this->property);
            } else {
                try {
                    $label = (string) $entity;
                } catch (\Exception $e) {
                    throw new \RuntimeException(sprintf("Unable to convert the entity %s to String, entity must have a '__toString()' method defined", ClassUtils::getClass($entity)), 0, $e);
                }
            }

            $result[] = $id;
            $result['_labels'][] = $label;
            $result['selected'][] = ["id" => $id,"text" => $label];
        }

        return $result;
    }
}
