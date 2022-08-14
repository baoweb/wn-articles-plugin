<?php namespace Baoweb\Articles;

use Backend\Models\User as UserModel;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
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

        /** Adding function that allows to check access to an article */
    }

    public function registerComponents()
    {
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
                'permissions' => [],
                'order'       => 600
            ]
        ];
    }
}
