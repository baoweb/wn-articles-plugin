<?php namespace Baoweb\Articles\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Baoweb\Articles\Classes\TestClass;
use Baoweb\Articles\Models\Settings;

class Articles extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Baoweb.Articles', 'articles', 'articles-side-menu');

        $this->addCss("/plugins/baoweb/articles/assets/css/forms.css");
    }

    public function update($recordId, $context = null)
    {
        $this->bodyClass = 'compact-container';

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function formExtendFields($form, $model)
    {
        if(!Settings::get('uses_attachments', true)) {
            $this->removeTabGroup('attachments', $form);
        }

        if(!Settings::get('uses_gallery', true)) {
            $this->removeTabGroup('gallery', $form);
        }


        if($model['title']->value == 'Test') {
            $formTransformer = new TestClass($form);

            $formTransformer->applyChangesToForm();
        }
    }

    protected function removeTabGroup($tab, $form)
    {

        foreach($form->tabs['fields'] as $key => $field) {
            if(starts_with($key, $tab)) {
                $form->removeField($key);
            }
        };
    }
}
