<?php

namespace Untek\Utility\Init\Domain\Entities;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Untek\Domain\Validator\Interfaces\ValidationByMetadataInterface;

class LockerEntity implements ValidationByMetadataInterface
{

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

    }

}
