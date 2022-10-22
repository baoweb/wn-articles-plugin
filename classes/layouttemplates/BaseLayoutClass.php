<?php namespace Baoweb\Articles\Classes\LayoutTemplates;

use Str;

class BaseLayoutClass
{
    protected function getTemplatePath($block)
    {
        $classArray = explode('\\', static::class);

        $class = end($classArray);

        $classDir = Str::lower($class);

        $classDir = Str::replace('class', '', $classDir);

        return __DIR__ . '/' . $classDir . '/' . $block . '.htm';
    }
}

