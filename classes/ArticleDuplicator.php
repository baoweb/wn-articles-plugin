<?php namespace Baoweb\Articles\Classes;

use Backend\Facades\BackendAuth;
use Baoweb\Articles\Models\Article;

class ArticleDuplicator
{
    public $duplicateTitle = false;

    public function duplicate($templateId)
    {
        $template = Article::findOrFail($templateId);

        $newArticle = new Article();

        $newArticle->title = '';
        $newArticle->template = $template->template;
        $newArticle->annotation = $template->annotation;
        $newArticle->content = $template->content;
        $newArticle->is_featured = $template->is_featured;
        $newArticle->has_long_title = $template->has_long_title;

        if($this->duplicateTitle) {
            $newArticle->title = '[kopie] ' . $template->title;
            $newArticle->long_title = '[kopie] ' . $template->long_title;
        }

        $newArticle->created_by = BackendAuth::getUser()->id;

        $newArticle->save();

        foreach($template->categories as $category) {
            $newArticle->categories()->attach($category->id);
        }

        $newArticle->save();

        return $newArticle;

    }
}
