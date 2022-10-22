<?php namespace Baoweb\Articles\Classes\LayoutTemplates;

use Baoweb\Articles\Models\Article;

interface LayoutTemplateInterface
{
    public function getName(): string;

    public function getKey(): string;

    public function applyChangesToForm($form): void;

    public function getRenderedArticle(Article $article): string;
}
