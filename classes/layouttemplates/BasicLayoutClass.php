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

    public function applyChangesToForm($formWidget): void
    {
        $formWidget->addTabFields([
            '_content_en' => [
                'tab' =>  'Obsah EN',
                'type' =>  'nestedform',
                'usePanelStyles' =>  false,
                'showPanel' =>  false,
                'form' =>  '$/baoweb/articles/config/forms/basic.yaml',
            ],
        ]);
    }

    public function getRenderedArticle(Article $article): string
    {
        return $article->content['content'];
    }
}
