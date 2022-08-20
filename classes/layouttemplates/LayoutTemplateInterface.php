<?php namespace Baoweb\Articles\Classes\LayoutTemplates
;

interface LayoutTemplateInterface
{
    public function getName(): string;

    public function getKey(): string;

    public function applyChangesToForm($form): void;
}
