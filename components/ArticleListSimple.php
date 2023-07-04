<?php namespace Baoweb\Articles\Components;

use Cms\Classes\ComponentBase;
use Baoweb\Articles\Models\Article;
use Baoweb\Articles\Models\Category;

class ArticleListSimple extends ComponentBase
{
    public $articles;

    public $category;

    public $annotation;

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
            'category' => [
                'title'   => 'Catgeory',
                'type'    => 'dropdown'
            ],
            'limit' => [
                'title'   => 'Limit',
                'type'    => 'string',
                'default' => 3,
            ],
            'annotation' => [
                'title'   => 'Anotace',
                'type'    => 'number',
                'default' => 0,
            ]
        ];
    }

    public function init()
    {
        $this->category = Category::find($this->properties['category']);

        $this->annotation = Category::find($this->properties['annotation']);

        if(!$this->category) {
            return [];
        }

        $this->articles = $this->category->articles()
            ->with('author')
            ->published()
            ->showInLists()
            ->where('category_id', $this->category->id)
            ->limit($this->properties['limit'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getCategoryOptions()
    {
        return Category::pluck('name', 'id')->toArray();
    }
}
