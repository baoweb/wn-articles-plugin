<?php namespace Baoweb\Articles\Components;

use Cms\Classes\ComponentBase;
use Baoweb\Articles\Models\Article;
use Baoweb\Articles\Models\Category;

class ArticleListCompact extends ComponentBase
{
    public $articles;

    public $category;

    /**
     * Gets the details for the component
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Compact article list',
            'description' => 'Article list showing only titles'
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
            ->published()
            ->showInLists()
            ->where('category_id', $this->properties['category'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(15);
    }

    public function getCategoryOptions()
    {
        return Category::pluck('name', 'id')->toArray();
    }
}
