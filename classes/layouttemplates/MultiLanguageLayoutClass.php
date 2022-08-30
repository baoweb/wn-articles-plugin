<?php namespace Baoweb\Articles\Classes\LayoutTemplates
;

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
                'form' =>  '$/baoweb/articles/forms/multi-lang.yaml',
            ],
        ]);

    }
}
