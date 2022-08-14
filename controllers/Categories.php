<?php namespace Baoweb\Articles\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\BackendAuth;
use Backend\Models\User;
use BackendMenu;

class Categories extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Baoweb.Articles', 'articles', 'categories');

        $user = BackendAuth::getUser();

        dd($user->canEditArticleCategory(1));


    }
}
