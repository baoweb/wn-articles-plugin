<?php namespace Baoweb\Articles\Components;

use Cms\Classes\ComponentBase;
use Baoweb\Articles\Models\Article;
use Baoweb\Articles\Models\Category;

class ArticleListSimple extends ComponentBase
{
    public $articles;

    public $category;

    /**
     * Gets the details for the component
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Simple article list',
            'description' => ''
        ];
    }

    /**
     * Returns the properties provided by the component
     */
    public function defineProperties()
    {
        return [
            'category' => [
                'title'   => 'Catgeory',
                'type'    => 'dropdown'
            ],
            'limit' => [
                'title'   => 'Limit',
                'type'    => 'number',
                'default' => 3,
            ]
        ];
    }

    public function init()
    {
        $this->category = Category::find($this->properties['category']);

        if(!$this->category) {
            return [];
        }

        $this->articles = $this->category->articles()
            ->with('author')
            ->where('is_published', 1)
            ->where('category_id', $this->category->id)
            ->limit($this->properties['limit'])
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getCategoryOptions()
    {
        return Category::pluck('name', 'id')->toArray();
    }
}
