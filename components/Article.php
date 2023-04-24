<?php namespace Baoweb\Articles\Components;

use App;
use Backend\Facades\BackendAuth;
use Baoweb\Articles\Classes\LayoutTemplates\LayoutTemplateInterface;
use Cms\Classes\ComponentBase;
use \Baoweb\Articles\Models\Article as ArticleModel;
use Illuminate\Support\Facades\Response;
use View;
use Winter\Storm\Support\Facades\DB;
use Winter\Storm\Support\Str;

class Article extends ComponentBase
{

    public $content = '';

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

        $articleQuery = ArticleModel::with(['attachments'])
            ->published();

        if(config('baoweb.articles::id_in_slug')) {

            $id = (int) Str::before($slug, '-');

            if(!$id) {
                return $this->controller->run('404');
            }

            $articleQuery->where(['id' => $id]);
        } else {
            $articleQuery->where(['slug' => $slug]);
        }

        $this->article = $articleQuery->first();

        // test if user is in backend and has access
        if(!$this->article && $user = BackendAuth::user()) {

            $articleQuery = ArticleModel::with(['attachments'])
                ->hasAccess();

            // load article again
            if(config('baoweb.articles::id_in_slug')) {

                $id = (int) Str::before($slug, '-');

                if(!$id) {
                    return $this->controller->run('404');
                }

                $articleQuery->where(['id' => $id]);
            } else {
                $articleQuery->where(['slug' => $slug]);
            }

            $this->article = $articleQuery->first();
        }

        if(!$this->article) {
            return $this->controller->run('404');
        }

        $title = $this->article->title;

        if($this->article->has_long_title && $this->article->long_title) {
            $title = $this->article->long_title;
        }

        $this->page->title = $title;
        $this->page->author =  $this->article->getAuthor();
        $this->page->published_at = $this->article->publishedAtForHumans();
        $this->page->hidePageHeader = $this->article->replacesHeader();
        $this->page->articleSettings = $this->article->settings;

        /* @var $layoutClass LayoutTemplateInterface */
        $layoutClass = App::make('baoweb.articles.layoutTemplates')->getLayoutInstance($this->article->template);

        $text = $layoutClass->getRenderedArticle($this->article);

        $this->content = $text;

        // incrementing
        $this->article->incrementCounter();
    }

}
