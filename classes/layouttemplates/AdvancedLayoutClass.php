<?php namespace Baoweb\Articles\Classes\LayoutTemplates
;

class AdvancedLayoutClass implements LayoutTemplateInterface {

    public function getName(): string
    {
        return 'Advanced layout';
    }

    public function getKey(): string
    {
        return 'advanced';
    }

    public function applyChangesToForm($formWidget): void
    {
        $formWidget->removeField('content');

        $formWidget->addTabFields([
            'content' => [
                'tab' =>  'Content',
                'type' =>  'nestedform',
                'usePanelStyles' =>  false,
                'showPanel' =>  false,
                'form' =>  '$/baoweb/articles/forms/with-gallery.yaml',
            ],
        ]);

    }
}
