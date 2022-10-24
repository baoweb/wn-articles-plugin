<?php namespace Baoweb\Articles;

use App;
use Backend\Models\User as UserModel;
use Baoweb\Articles\Classes\LayoutRegistry;
use Baoweb\Articles\Classes\LayoutTemplates\AdvancedLayoutClass;
use Baoweb\Articles\Classes\LayoutTemplates\BasicLayoutClass;
use Baoweb\Articles\Classes\LayoutTemplates\MultiLanguageLayoutClass;
use Baoweb\Articles\Components\Article;
use Baoweb\Articles\Components\ArticleListSimple;
use Baoweb\Articles\Models\Category;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function register()
    {
        App::singleton('baoweb.articles.layoutTemplates', function() {
            return new LayoutRegistry();
        });

        App::make('baoweb.articles.layoutTemplates')->registerLayoutTemplate(new BasicLayoutClass());

        // App::make('baoweb.articles.layoutTemplates')->registerLayoutTemplate(new MultiLanguageLayoutClass());

        App::make('baoweb.articles.layoutTemplates')->registerLayoutTemplate(new AdvancedLayoutClass());
    }

    public function boot()
    {
        /** Adding function that allows to check access to category */
        UserModel::extend(function($model) {
            $model->addDynamicMethod('canEditArticleCategory', function($category) use ($model) {

                if($model->hasAccess('baoweb.articles.edit-all-articles')) {
                    return true;
                }

                return false;
            });
        });

        UserModel::extend(function($model){
            $model->belongsToMany['baowebArticleCategories'] = [
                Category::class,
                'table'    => 'baoweb_articles_users_categories',
                'key'      => 'user_id',
                'otherKey' => 'category_id'
            ];
        });

        /** Adding function that allows to check access to an article */
    }

    public function registerComponents()
    {
        return [
            Article::class => 'article',
            ArticleListSimple::class => 'articleListSimple'
        ];
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'baoweb.articles::lang.plugin.name',
                'icon'        => 'icon-wrench',
                'category'    => 'Articles',
                'description' => 'baoweb.articles::lang.plugin.name',
                'class'       => 'Baoweb\Articles\Models\Settings',
                'permissions' => ['assign-rights'],
                'order'       => 600
            ]
        ];
    }
}
