<?php namespace Baoweb\Articles\Components;

use Cms\Classes\ComponentBase;
use Baoweb\Articles\Models\Article;
use Baoweb\Articles\Models\Category;
use Winter\Storm\Support\Str;

class ArticleListSimple2 extends ComponentBase
{
    public $articles;

    public $category;

    public $moreArticle;

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
            ],
            'articleSlug' => [
                'title'   => 'Article slug for "more" link',
                'type'    => 'string',
                'default' => '',
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

        if (!empty($this->properties['articleSlug'])) {
            $slug = $this->properties['articleSlug'];
            $query = Article::published();

            if (config('baoweb.articles::id_in_slug')) {
                $id = (int) Str::before($slug, '-');
                $query->where('id', $id);
            } else {
                $query->where('slug', $slug);
            }

            $this->moreArticle = $query->first();
        }
    }
}
