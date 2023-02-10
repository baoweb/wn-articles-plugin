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
        // TODO figure out langugae
        $lang = explode('/', request()->path());

        if($lang[0] == 'en') {
            $content = $article->_content_en;
        } else {
            $content = $article->content;
        }

        return $content['content'];
    }
}
