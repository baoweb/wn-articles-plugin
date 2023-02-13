<?php namespace Baoweb\Articles\Classes\LayoutTemplates
;

use Baoweb\Articles\Models\Article;
use Winter\Storm\Support\Facades\Twig;

class HtmlLayoutClass extends BaseLayoutClass implements LayoutTemplateInterface {

    public function getName(): string
    {
        return 'HTML layout';
    }

    public function getKey(): string
    {
        return 'html';
    }

    public function applyChangesToForm($formWidget): void
    {
        $formWidget->removeField('content');
        $formWidget->removeField('_content_en');

        $formWidget->addTabFields([
            'content' => [
                'tab' =>  'Obsah',
                'type' =>  'nestedform',
                'usePanelStyles' =>  false,
                'showPanel' =>  false,
                'form' =>  '$/baoweb/articles/config/forms/html.yaml',
            ],
            '_content_en' => [
                'tab' =>  'Obsah EN',
                'type' =>  'nestedform',
                'usePanelStyles' =>  false,
                'showPanel' =>  false,
                'form' =>  '$/baoweb/articles/config/forms/html.yaml',
            ],
        ]);

    }

    public function getRenderedArticle(Article $article): string
    {
        // TODO figure out langugae
        $lang = explode('/', request()->path());

        if($lang[0] == 'en') {
            $content = $article->_content_en;
        } else {
            $content = $article->content;
        }


        $output = '';

        if(empty($content['body_groups'])) {
            return '';
        }

        foreach($content['body_groups'] as $group) {
            $templatePath = $this->getTemplatePath($group['_group']);

            $template = file_get_contents($templatePath);

            $vars = $group;

            if ($group['_group'] == 'documents') {
                foreach($article->attachments as $attachment) {
                    $attachment->file_size_for_humans = round($attachment->file_size / 10000) / 100 . ' Mb';
                }
            }

            $vars['article'] = $article;

            $parsedTemplate = Twig::parse($template, $vars);

            $output .= $parsedTemplate;
        }

        return $output;
    }
}
