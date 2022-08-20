<?php namespace Baoweb\Articles\Classes;

use Baoweb\Articles\Classes\LayoutTemplates\LayoutTemplateInterface;

class LayoutRegistry {

    protected $layouts = [];

    protected $layoutNames = [];

    public function registerLayoutTemplate(LayoutTemplateInterface $template): void
    {
        $this->layouts[$template->getKey()] = $template::class;

        $this->layoutNames[$template->getKey()] = $template->getName();
    }

    public function getLayoutTemplateNames(): array
    {
        return $this->layoutNames;
    }

    public function getLayoutTemplateClasses(): array
    {
        return $this->layouts;
    }

}
