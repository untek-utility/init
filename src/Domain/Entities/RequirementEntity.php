<?php

namespace Untek\Utility\Init\Domain\Entities;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Untek\Domain\Validator\Interfaces\ValidationByMetadataInterface;

class RequirementEntity implements ValidationByMetadataInterface
{

    private $name = null;

    private $mandatory = null;

    private $condition = null;

    private $by = null;

    private $memo = null;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank);
        $metadata->addPropertyConstraint('mandatory', new Assert\NotBlank);
        $metadata->addPropertyConstraint('condition', new Assert\NotBlank);
        $metadata->addPropertyConstraint('by', new Assert\NotBlank);
        $metadata->addPropertyConstraint('memo', new Assert\NotBlank);
    }

    public function setName($value) : void
    {
        $this->name = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setMandatory($value) : void
    {
        $this->mandatory = $value;
    }

    public function getMandatory()
    {
        return $this->mandatory;
    }

    public function setCondition($value) : void
    {
        $this->condition = $value;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function setBy($value) : void
    {
        $this->by = $value;
    }

    public function getBy()
    {
        return $this->by;
    }

    public function setMemo($value) : void
    {
        $this->memo = $value;
    }

    public function getMemo()
    {
        return $this->memo;
    }


}

