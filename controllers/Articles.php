<?php namespace Baoweb\Articles\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

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
}
