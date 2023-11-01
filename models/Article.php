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
        'long_title',
        'annotation',
        'slug',
    ];

    protected $dates = [
        'deleted_at',
        'published_at',
        'publish_at',
        'unpublish_at',
    ];

    protected $jsonable = [
        'content',
        'settings',
    ];

    public $belongsTo = [
        'author' => [
            User::class,
            'key' => 'created_by'
        ],
        'primaryCategory' => [
            Category::class,
            'key' => 'primary_category'
        ]
    ];

    public $belongsToMany = [
        'categories' => [
            Category::class,
            'table'    => 'baoweb_articles_articles_categories',
            'key'      => 'article_id',
            'otherKey' => 'category_id',
            'order' => 'internal_name',
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
        'title' => 'required',
        'template' => 'required'
    ];

    public $customMessages = [
        'required' => 'Vyplňte prosím pole :attribute.',
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

        if($this->is_published != 2) {
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

            if($user->hasAccess(['edit-all-articles'])) {
                return;
            }

            if($user->hasAccess(['edit-selected-articles']) ) {
                $categories = DB::table('baoweb_articles_users_categories')
                    ->where('user_id', $user->id)
                    ->pluck('category_id');

                /*
                $builder->whereHas('categories', function ($query) use ($categories) {
                    $query->whereIn('baoweb_articles_categories.id', $categories);
                });
                */
                $builder->where(function($query) use ($user, $categories) {
                    $query->whereHas('categories', function ($query) use ($categories) {
                        $query->whereIn('baoweb_articles_categories.id', $categories);
                    })->orWhere('created_by', $user->id);
                });

                return;
            }

           // fallback if no other option
           $builder->where('created_by', $user->id);
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

    public function scopeFilterByAuthor($query, $filter)
    {
        return $query->whereIn('created_by', $filter);
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

        return Str::limit($text, 100, ' ...');
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
        return Category::query()
            ->active()
            ->orderBy('internal_name')
            ->pluck('internal_name', 'id');
    }

    public function generateSlug()
    {
        if(config('baoweb.articles::id_in_slug')) {
            return $this->id . '-' .$this->slug;
        }

        return $this->slug;
    }

    public function replacesHeader()
    {
        if($this->template == 'advanced') {
            if(!isset($this->content['body_groups']) || !count($this->content['body_groups'])) {
                return false;
            };

            $firstGroup = array_values($this->content['body_groups'])[0];

            if($firstGroup['_group'] == 'project_header_3' || $firstGroup['_group'] == 'project_header_2') {
                return true;
            }
         };

        return false;
    }

    public function filterFields($fields, $context = null)
    {
        if (!isset($fields->author)) {
            return;
        }

        $user = BackendAuth::getUser();

        if (!$user->hasAccess(['edit-author'])) {
            $fields->author->readOnly = true;

            $fields->custom_author->hidden = true;
        } else {
            $fields->author->readOnly = false;
        }
    }

    public function isPublished()
    {
        if($this->is_published == 1) {
            return true;
        }

        if(
            $this->is_published == 2 &&
            $this->publish_at?->lt(Carbon::NOW()) &&
            $this->unpublish_at == null
        ) {
            return true;
        }

        if(
            $this->is_published == 2 &&
            $this->publish_at?->lt(Carbon::NOW()) &&
            $this->unpublish_at?->gt(Carbon::NOW())
        ) {
            return true;
        }

        return false;
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    /*  Scopes                                                                                                        */
    /* -------------------------------------------------------------------------------------------------------------- */

    public function scopePublished($query)
    {
        return $query->where('is_published', 1)
            ->orWhere(function ($query) {
                $query->where('is_published', 2)
                    ->whereTime('publish_at', '<=', Carbon::now() )
                    ->whereNull('unpublish_at');
            })
            ->orWhere(function ($query) {
                $query->where('is_published', 2)
                    ->whereTime('publish_at', '<=', Carbon::now() )
                    ->whereTime('unpublish_at', '>=', Carbon::now() );
            });
    }

    public function scopeHasNoCategory($query)
    {
        return $query->doesntHave('categories');
    }

    public function scopeShowInLists($query)
    {
        return $query->where('hide_in_lists', false);
    }

    public function scopeHasAccess($query)
    {
        $user = BackendAuth::getUser();

        if($user->hasAccess(['edit-all-articles'])) {
            return $query;
        }

        if($user->hasAccess(['edit-selected-articles']) ) {
            $categories = DB::table('baoweb_articles_users_categories')
                ->where('user_id', $user->id)
                ->pluck('category_id');

            /*
            $query->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('baoweb_articles_categories.id', $categories);
            });
            */

            $query->where(function($query) use ($user, $categories) {
                $query->whereHas('categories', function ($query) use ($categories) {
                    $query->whereIn('baoweb_articles_categories.id', $categories);
                })->orWhere('created_by', $user->id);
            });

            return $query;
        }

        // fallback if no other option
        return $query->where('created_by', $user->id);
    }

}
