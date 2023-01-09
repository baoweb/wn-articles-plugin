<?php namespace Baoweb\Articles\Models;

use App;
use Backend\Models\User;
use Baoweb\Articles\Classes\LayoutTemplates\LayoutTemplateInterface;
use Baoweb\Articles\Controllers\Categories;
use Baoweb\SimpleCounter\Classes\SimpleCounter;
use Carbon\Carbon;
use http\Client\Request;
use Model;
use Backend\Facades\BackendAuth;
use Winter\Storm\Database\Builder;
use Winter\Storm\Support\Facades\DB;
use Winter\Storm\Support\Str;

/**
 * Model
 */
class Article extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    use \Winter\Storm\Database\Traits\SoftDelete;

    use SimpleCounter;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'title',
        'annotation',
        'slug',
    ];

    protected $dates = [
        'deleted_at',
        'published_at',
        'publish_at',
        'unpublish_at',
    ];

    protected $jsonable = ['content'];

    public $belongsTo = [
        'author' => [
            User::class,
            'key' => 'created_by'
        ]
    ];

    public $belongsToMany = [
        'categories' => [
            Category::class,
            'table'    => 'baoweb_articles_articles_categories',
            'key'      => 'article_id',
            'otherKey' => 'category_id',
            'order' => 'name',
        ]
    ];

    public $attachMany = [
        'galleryPhotos' => ['System\Models\File', 'public' => false ],
        'attachments' => ['System\Models\File', 'public' => false ],
    ];

    public $attachOne = [
        'coverImage' => 'System\Models\File',
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'baoweb_articles_articles';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public function beforeCreate()
    {
        $this->created_by = BackendAuth::getUser()->id;
    }

    public function beforeSave()
    {
        // this is only because eof translation
        if(post('Article') && isset(post('Article')['_content_en'])) {
            $contentEn = post('Article')['_content_en'];

            $content = $this->content;

            $content['translations']['en'] = $contentEn;

            $this->content = $content;
        }

        if($trans = post('RLTranslate')) {
            $cs = false;
            $en = false;

            if(
                trim($trans['cs']['title']) ||
                trim($trans['cs']['slug']) ||
                trim($trans['cs']['annotation'])
            ) {
                $cs = true;
            }

            if(
                trim($trans['en']['slug']) ||
                trim($trans['en']['title']) ||
                trim($trans['en']['annotation'])
            ) {
                $en = true;
            }

            $lang = 'cs';

            if ($cs && $en) {
                $lang = 'both';
            } elseif (!$cs && $en) {
                $lang = 'en';
            }

            $this->language = $lang;

        }


        if(!$this->published_at && $this->is_published) {
            $this->published_at = Carbon::now();
        }

        if(!$this->is_scheduled) {
            $this->publish_at = null;
            $this->unpublish_at = null;
        }
    }

    protected static function booted()
    {
        static::addGlobalScope('onlyAllowed', function (Builder $builder) {

            $user = BackendAuth::getUser();

            if(!App::runningInBackend()) {
                return;
            }

            if(isset($user->role->permissions['edit-all-articles']) && $user->role->permissions['edit-all-articles']) {
                return;
            }

            $categories = DB::table('baoweb_articles_users_categories')
                ->where('user_id', $user->id)
                ->pluck('category_id');

            $builder->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('baoweb_articles_categories.id', $categories);
            });
        });
    }

    public function afterFetch()
    {
        $english = [];

        if(isset($this->content['translations'])) {
            $english = $this->content['translations']['en'];
        }

        $output = [];

        if($this->content) {
            foreach($this->content as $key => $value) {
                if($key !== 'translations') {
                    $output[$key] = $value;
                }
            }
        }


        $this->_content_en = $english;

        $this->content = $output;
    }

    public function scopeFilterByCategory($query, $filter)
    {
        return $query->whereHas('categories', function($category) use ($filter) {
            $category->whereIn('category_id', $filter);
        });
    }

    public function getTemplateOptions($value, $formData)
    {

        return App::make('baoweb.articles.layoutTemplates')->getLayoutTemplateNames();
    }

    /*
    public function categoriesOption()
    {
        return [
            1 => 'aaa',
            2 => 'bbb'
        ];
    }

    public function beforeSave()
    {
        $data = post();

        if(isset($data['categories'])) {
            print_r($data['categories']);

            dd();
        }
    }
    */

    public function scopeTemplates($query)
    {
        $query->where('is_template', true);
    }

    public function filterFields($fields, $context = null)
    {
        if (!isset($fields->author)) {
            return;
        }

        $user = BackendAuth::getUser();

        if (!$user->hasAccess(['baoweb.articles.edit-author'])) {
            $fields->author->disabled = true;

            $fields->custom_author->hidden = true;
        } else {
            $fields->author->disabled = false;
        }
    }

    public function getAuthor()
    {
        if($this->custom_author) {
            return $this->custom_author;
        }

        return $this->author->first_name . ' ' . $this->author->last_name;
    }

    public function getAnnotation()
    {
        if($this->annotation) {
            return strip_tags($this->annotation);
        }

        /* @var $layoutClass LayoutTemplateInterface */
        $layoutClass = App::make('baoweb.articles.layoutTemplates')->getLayoutInstance($this->template);

        $text = $layoutClass->getRenderedArticle($this);

        $text = strip_tags($text);

        return Str::limit($text, 500, ' ...');
    }

    public function publishedAtForHumans()
    {
        if($this->published_at) {
            return $this->published_at?->format(config('baoweb.articles::date_format'));
        }

        if( $this->publish_at && $this->publish_at?->lt(Carbon::now()) ) {
            return $this->publish_at?->format(config('baoweb.articles::date_format'));
        }

        return false;
    }

    public function getCategoryListingOptions()
    {
        return Category::pluck('name', 'id');
    }

    public function generateSlug()
    {
        if(config('baoweb.articles::id_in_slug')) {
            return $this->id . '-' .$this->slug;
        }

        return $this->slug;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
