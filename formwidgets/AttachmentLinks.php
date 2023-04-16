<?php namespace Baoweb\Articles\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;

/**
 * AttachmentLinks Form Widget
 */
class AttachmentLinks extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'baoweb_articles_attachment_links';

    /**
     * @inheritDoc
     */
    public function init()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('attachmentlinks');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['model'] = $this->model;
        $this->vars['attachments'] = $this->model->{$this->config->relation};

        if($this->config->relation == 'galleryPhotos') {
            $this->vars['type'] = 'gallery';
        } else {
            $this->vars['type'] = 'fiels';
        }
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/attachmentlinks.css', 'Baoweb.Articles');
        $this->addJs('js/attachmentlinks.js', 'Baoweb.Articles');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return FormField::NO_SAVE_DATA;
    }
}
