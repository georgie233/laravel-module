<?php
/** .-------------------------------------------------------------------
 * |      Site: www.hdcms.com
 * |      Date: 2018/6/25 下午3:13
 * |    Author: 向军大叔 <2300071698@qq.com>
 * '-------------------------------------------------------------------*/

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
