<?php

declare(strict_types=1);

namespace Kanvas\Sdk\Resources;

use Kanvas\Sdk\Resources;
use Kanvas\Sdk\Traits\CrudOperationsTrait;

class UserRoles extends Resources
{
    const RESOURCE_NAME = 'users-roles';

    use CrudOperationsTrait;
}
