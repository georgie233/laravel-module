<?php
namespace Georgie\Module\Traits;

use Module;

trait ModuleService
{
    /**
     * 当前模块
     *
     * @return mixed
     */
    public function currentModule()
    {
        $controller = \Route::getCurrentRoute()->getAction()['controller'];
        preg_match('@\\\(.*?)\\\@i', $controller, $match);

        return $module = ($match[1]);
    }

    public function getModulesLists($filter = [])
    {
        $modules = [];
        foreach (\Module::getOrdered() as $module) {
            if ( ! in_array($module->name, $filter)) {
                $modules[] = [
                    'name'   => $module->name,
                    'title'  => \GModule::config($module->name.'.config.name'),
                    'module' => $module,
                ];
            }
        }

        return $modules;
    }

    /**
     * 获取模块
     *
     * @param null $module
     *
     * @return mixed|void
     */
    public function module($module = null)
    {
        $module = $module ?? $this->currentModule();

        return Module::find($module);
    }

    /**
     * 获取模块路径
     *
     * @param null $module
     *
     * @return mixed
     */
    public function getModulePath($module = null)
    {
        return Module::find($this->module($module))->getPath();
    }
}
