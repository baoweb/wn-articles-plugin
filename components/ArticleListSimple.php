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
            'name'        => 'ArtileListSimple Component',
            'description' => 'No description provided yet...'
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
            ]
        ];
    }

    public function init()
    {
        $this->category = Category::findOrFail($this->properties['category']);

        $this->articles = $this->category->articles()
            ->with('author')
            ->where('is_published', 1)
            ->where('category_id', 1)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getCategoryOptions()
    {
        return Category::pluck('name', 'id')->toArray();
    }
}
