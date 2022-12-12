<?php namespace Baoweb\Articles\Models;

use App;
use Backend\Facades\BackendAuth;
use Model;
use Winter\Storm\Database\Builder;
use Winter\Storm\Support\Facades\DB;

/**
 * Model
 */
class Category extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    use \Winter\Storm\Database\Traits\SoftDelete;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];

    protected $dates = ['deleted_at'];

    public $translatable = [
        'name',
        'slug',
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'baoweb_articles_categories';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsToMany = [
        'articles' => [
            Article::class,
            'table'    => 'baoweb_articles_articles_categories',
            'key'      => 'category_id',
            'otherKey' => 'article_id'
        ]
    ];

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

            $builder->whereIn('baoweb_articles_categories.id', $categories);
        });
    }

    public function getLabelAttribute()
    {
        return $this->internal_name ?: $this->name;
    }

    public function getCategoryOptions()
    {
        $categories = Category::select('id', 'name', 'internal_name')
            ->orderBy('name')
            ->get();

        $output = [];

        foreach($categories as $category) {
            $output[$category->id] = $category->label;
        }

        return $output;
    }
}
