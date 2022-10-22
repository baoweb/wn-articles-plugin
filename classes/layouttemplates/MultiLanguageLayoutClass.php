<?php namespace Baoweb\Articles\Classes\LayoutTemplates
;

use Baoweb\Articles\Models\Article;

class MultiLanguageLayoutClass implements LayoutTemplateInterface {

    public function getName(): string
    {
        return 'Multi language layout';
    }

    public function getKey(): string
    {
        return 'multi-lang';
    }

    public function applyChangesToForm($formWidget): void
    {
        $formWidget->removeField('content');

        $formWidget->removeField('annotation');

        $formWidget->addTabFields([
            'content' => [
                'tab' =>  'Content',
                'type' =>  'nestedform',
                'usePanelStyles' =>  false,
                'showPanel' =>  false,
                'form' =>  '$/baoweb/articles/config/forms/multi-lang.yaml',
            ],
        ]);

    }

    public function getRenderedArticle(Article $article): string
    {
        // TODO: Implement getRenderedArticle() method.
    }
}
