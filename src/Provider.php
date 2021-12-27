<?php

namespace Georgie\Module;

use Georgie\Module\Traits\ConfigService;
use Georgie\Module\Traits\MenusService;
use Georgie\Module\Traits\ModuleService;
use Georgie\Module\Traits\PermissionService;

/**
 * Class Facade
 *
 * @package Georgie\Module
 */
class Provider
{
    use ConfigService, PermissionService, MenusService,ModuleService;
}
