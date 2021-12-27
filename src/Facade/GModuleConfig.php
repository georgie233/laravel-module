<?php
namespace Georgie\Module\Facade;

class GModuleConfig extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'GModule';
    }
}
