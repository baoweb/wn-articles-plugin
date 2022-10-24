<?php namespace Baoweb\Articles\Classes\LayoutTemplates;

use Baoweb\Articles\Models\Article;

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

    public function getRenderedArticle(Article $article): string
    {
        return $article->content['content'];
    }
}
