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
                'name'        => 'Article list',
                'description' => 'Article list with annotations'
            ]
        ];
    }

    public function init()
    {
        $this->category = Category::findOrFail($this->properties['category']);

        $this->page->title = $this->category->name;

        $this->articles = $this->category->articles()
            ->with('author')
            ->published()
            ->where('category_id', $this->properties['category'])
            ->where('is_template', false)
            ->orderBy('published_at', 'desc')
            ->paginate(15);
    }

    public function getCategoryOptions()
    {
        return Category::pluck('name', 'id')->toArray();
    }
}
