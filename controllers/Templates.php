<?php namespace Baoweb\Articles\Controllers;

use App;
use Backend\Classes\Controller;
use BackendMenu;
use Baoweb\Articles\Classes\LayoutRegistry;
use Baoweb\Articles\Classes\TestClass;
use Baoweb\Articles\Models\Settings;

class Templates extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'edit-templates'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Baoweb.Articles', 'articles', 'templates-side-menu');

        $this->addCss("/plugins/baoweb/articles/assets/css/forms.css");
    }

    public function listExtendQuery($query, $definition)
    {
        $query->where('is_template', true);
    }

    public function update($recordId, $context = null)
    {
        $this->bodyClass = 'compact-container';

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function formExtendFields($form, $model)
    {
        /** @var $templateRegistry LayoutRegistry */
        $templateRegistry = App::make('baoweb.articles.layoutTemplates');

        // remove attachments and gallery for templates
        $this->removeTabGroup('attachments', $form);

        $this->removeTabGroup('gallery', $form);

        foreach($templateRegistry->getLayoutTemplateClasses() as $key => $templateClassName) {
            if($model['template']->value == $key) {
                $templateClass = new $templateClassName;

                $templateClass->applyChangesToForm($form);

                break;
            }
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
