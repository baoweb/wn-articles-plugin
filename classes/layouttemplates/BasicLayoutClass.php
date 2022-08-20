<?php namespace Baoweb\Articles\Classes\LayoutTemplates;

class BasicLayoutClass implements LayoutTemplateInterface {

    public function getName(): string
    {
        return 'Basic layout';
    }

    public function getKey(): string
    {
        return 'basic';
    }

    public function applyChangesToForm($form): void {}
}
