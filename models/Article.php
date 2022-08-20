<?php namespace Baoweb\Articles\Models;

use App;
use Backend\Models\User;
use Baoweb\Articles\Controllers\Categories;
use http\Client\Request;
use Model;
use Backend\Facades\BackendAuth;
use Winter\Storm\Database\Builder;
use Winter\Storm\Support\Facades\DB;

/**
 * Model
 */
class Article extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    use \Winter\Storm\Database\Traits\SoftDelete;

    public $implement = ['Winter.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'title',
        'annotation',
        'content',
    ];

    protected $dates = ['deleted_at'];

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
            'otherKey' => 'category_id'
        ]
    ];

    public $attachMany = [
        'galleryPhotos' => 'System\Models\File',
        'attachments' => 'System\Models\File',
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

    public function scopeFilterByCategory($query, $filter)
    {
        return $query->whereHas('categories', function($category) use ($filter) {
            $category->whereIn('baoweb_articles_articles_categories.id', $filter);
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


}
