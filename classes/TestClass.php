<?php namespace Baoweb\Articles\Classes;

class TestClass {

    protected $form;

    public function __construct($formWidget)
    {
        $this->form = $formWidget;
    }

    public function applyChangesToForm() {

        $this->form->removeField('content');

        $this->form->addTabFields([
            'author' => [
                'label'   => 'My Field',
                'comment' => 'This is a custom field I have added.',
            ],
        ]);

    }

}
