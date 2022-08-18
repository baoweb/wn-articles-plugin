<?php namespace Baoweb\Articles\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\Backend;
use Backend\Models\User;
use BackendMenu;
use Baoweb\Articles\Classes\TestClass;
use Baoweb\Articles\Models\Category;
use Redirect;
use Winter\Storm\Router\Helper as RouterHelper;

class Users extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
    ];

    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Baoweb.Articles', 'articles', 'users-right');

        // $this->addCss("/plugins/baoweb/articles/assets/css/forms.css");
    }

    public function listExtendQuery($query)
    {
        // TODO change up to filter only right

        $query
            ->with('role')
            ->whereHas('role', function($q){
                $q->where('code', 'restricted-editors');
            })->get();
    }

    public function update(int $id)
    {
        $categories = Category::orderBy('name')->get();

        $this->vars['user'] = User::with('baowebArticleCategories')->findOrFail($id);
        $this->vars['categories'] = $categories;
    }


    public function onSave($id)
    {
        print_r(post());
        dd();

        if ($redirect = $this->makeRedirect()) {
            return $redirect;
        }
    }

    public function save($id)
    {
        $user = User::findOrFail($id);

        $user->baowebArticleCategories()->detach();

        $categories = Category::orderBy('name')->pluck('id');

        $allowedCategories = post('categories');

        foreach($categories as $category) {
            if(isset($allowedCategories[$category])) {
                $user->baowebArticleCategories()->attach($category);
            }
        }

        //$close = post('close');
        $close = true;

        $redirectUrl = Backend::redirect('/baoweb/articles/users');

        if ($close) {
            return $redirectUrl;
        }
    }

    public function makeRedirect()
    {
        $redirectUrl = Backend::redirect('/baoweb/articles/users');

        if ($close = post('close')) {
            return $redirectUrl;
        }
    }
}
