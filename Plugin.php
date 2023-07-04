<?php namespace Baoweb\Articles;

use App;
use Backend\Models\User as UserModel;
use Baoweb\Articles\Classes\LayoutRegistry;
use Baoweb\Articles\Classes\LayoutTemplates\AdvancedLayoutClass;
use Baoweb\Articles\Classes\LayoutTemplates\BasicLayoutClass;
use Baoweb\Articles\Classes\LayoutTemplates\HtmlLayoutClass;
use Baoweb\Articles\Classes\LayoutTemplates\LayoutTemplateInterface;
use Baoweb\Articles\Classes\LayoutTemplates\MultiLanguageLayoutClass;
use Baoweb\Articles\Components\Article;
use Baoweb\Articles\Components\ArticleList;
use Baoweb\Articles\Components\ArticleListCompact;
use Baoweb\Articles\Components\ArticleListFromHomepage;
use Baoweb\Articles\Components\ArticleListSimple;
use Baoweb\Articles\Components\ArticleListSimple2;
use Baoweb\Articles\FormWidgets\AttachmentLinks;
use Baoweb\Articles\Models\Category;
use System\Classes\PluginBase;
use Winter\Storm\Support\Facades\Event;

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

        App::make('baoweb.articles.layoutTemplates')->registerLayoutTemplate(new HtmlLayoutClass());
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


        \Event::listen('offline.sitesearch.query', function ($query) {

            // The controller is used to generate page URLs.
            $controller = \Cms\Classes\Controller::getController() ?? new \Cms\Classes\Controller();

            // Search your plugin's contents
            $items = \Baoweb\Articles\Models\Article
                ::where('title', 'like', "%${query}%")
                ->orWhere('content', 'like', "%${query}%")
                ->get();

            // Now build a results array
            $results = $items->map(function ($item) use ($query, $controller) {

                // If the query is found in the title, set a relevance of 2
                $relevance = mb_stripos($item->title, $query) !== false ? 2 : 1;

                // Optional: Add an age penalty to older results. This makes sure that
                // newer results are listed first.
                if ($relevance > 1 && $item->created_at) {
                   $ageInDays = $item->created_at->diffInDays(\Illuminate\Support\Carbon::now());
                   $relevance -= \OFFLINE\SiteSearch\Classes\Providers\ResultsProvider::agePenaltyForDays($ageInDays);
                }

                /* @var $layoutClass LayoutTemplateInterface */
                $layoutClass = App::make('baoweb.articles.layoutTemplates')->getLayoutInstance($item->template);

                $content = $layoutClass->getRenderedArticle($item);

                return [
                    'title'     => $item->title,
                    'text'      => $content,
                    'url'       => $controller->pageUrl('article', ['slug' => $item->generateSlug()]),
                    // 'thumb'     => optional($item->images)->first(), // Instance of System\Models\File
                    'relevance' => $relevance, // higher relevance results in a higher
                    // position in the results listing
                    // 'meta' => 'data',       // optional, any other information you want
                    // to associate with this result
                    // 'model' => $item,       // optional, pass along the original model
                ];
            });

            return [
                'provider' => __('stránka'), // The badge to display for this result
                'results'  => $results,
            ];
        });
    }

    public function registerComponents()
    {
        return [
            Article::class => 'article',
            ArticleListSimple::class => 'articleListSimple',
            ArticleListSimple2::class => 'articleListSimple2',
            ArticleList::class => 'articleList',
            ArticleListCompact::class => 'articleListCompact',
            ArticleListFromHomepage::class => 'articleListFromHomepage',
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

    public function registerFormWidgets()
    {
        return [
            AttachmentLinks::class => 'attachmentlinks',
        ];
    }
}
