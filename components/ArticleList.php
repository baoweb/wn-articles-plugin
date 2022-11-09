<?php namespace Baoweb\Articles\Components;

use Cms\Classes\ComponentBase;
use Baoweb\Articles\Models\Article;
use Baoweb\Articles\Models\Category;

class ArticleList extends ComponentBase
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

        $this->page->title = $this->category->name;

        $this->articles = $this->category->articles()
            ->with('author')
            ->where('is_published', 1)
            ->where('category_id', $this->properties['category'])
            ->orderBy('published_at', 'desc')
            ->paginate(15);
    }

    public function getCategoryOptions()
    {
        return Category::pluck('name', 'id')->toArray();
    }
}
