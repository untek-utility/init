<?php

namespace Untek\Utility\Init\Domain\Services;

use YiiRequirementChecker;
use Untek\Model\Service\Base\BaseService;
use Untek\Utility\Init\Domain\Helpers\RequirementHelper;
use Untek\Utility\Init\Domain\Interfaces\Repositories\RequirementRepositoryInterface;
use Untek\Utility\Init\Domain\Interfaces\Services\RequirementServiceInterface;

class RequirementService extends BaseService implements RequirementServiceInterface
{

    private $requirementChecker;

    public function __construct(RequirementRepositoryInterface $repository, YiiRequirementChecker $requirementChecker)
    {
        $this->setRepository($repository);
        $this->requirementChecker = $requirementChecker;
    }

    public function checkRequirements(): array
    {
        $requirements = $this->getRepository()->findAll();
        $result = RequirementHelper::check($requirements);
        return $result;
    }
}
