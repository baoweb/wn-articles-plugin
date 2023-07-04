<?php namespace Baoweb\Articles\Components;

use Cms\Classes\ComponentBase;
use Baoweb\Articles\Models\Article;
use Baoweb\Articles\Models\Category;

class ArticleListSimple2 extends ComponentBase
{
    public $articles;

    public $category;

    /**
     * Gets the details for the component
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Box - výpis článků',
            'description' => 'Výpis článků na hlavní straně'
        ];
    }

    /**
     * Returns the properties provided by the component
     */
    public function defineProperties()
    {
        return [
            'limit' => [
                'title'   => 'Limit',
                'type'    => 'string',
                'default' => 3,
            ]
        ];
    }

    public function init()
    {
        $this->articles = Article::with('author')
            ->published()
            ->where('is_news_item', true)
            ->limit($this->properties['limit'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
