<?php namespace Baoweb\Articles\Components;

use App;
use Baoweb\Articles\Classes\LayoutTemplates\LayoutTemplateInterface;
use Cms\Classes\ComponentBase;
use \Baoweb\Articles\Models\Article as ArticleModel;
use Illuminate\Support\Facades\Response;
use View;

class Article extends ComponentBase
{

    public $content = 'aaa';

    protected $article;

    /**
     * Gets the details for the component
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Article Component',
            'description' => 'No description provided yet...'
        ];
    }

    /**
     * Returns the properties provided by the component
     */
    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
        $slug = $this->param('slug');

        $this->article = ArticleModel::with(['attachments'])
            ->where(['slug' => $slug])->first();

        if(!$this->article) {
            return $this->controller->run('404');
        }

        $this->page->title = $this->article->title;
        $this->page->author =  $this->article->getAuthor();
        $this->page->published_at = $this->article->publishedAtForHumans();

        /* @var $layoutClass LayoutTemplateInterface */
        $layoutClass = App::make('baoweb.articles.layoutTemplates')->getLayoutInstance($this->article->template);

        $text = $layoutClass->getRenderedArticle($this->article);

        $this->content = $text;
    }

    public function onRun()
    {

    }
}
