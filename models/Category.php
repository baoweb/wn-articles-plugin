<?php namespace Baoweb\Articles\Models;

use App;
use Backend\Facades\BackendAuth;
use Baoweb\SimpleCounter\Classes\SimpleCounter;
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

    use SimpleCounter;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];

    protected $dates = ['deleted_at'];

    public $translatable = [
        'name',
        'slug',
        'text',
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
            ->orderBy('internal_name')
            ->get();

        $output = [];

        foreach($categories as $category) {
            $output[$category->id] = $category->label;
        }

        return $output;
    }

    public function filterFields($fields, $context = null)
    {
        $user = BackendAuth::getUser();

        if (!$user->hasAccess(['edit_all_category_fields'])) {
            $fields->name->disabled = true;
            $fields->internal_name->disabled = true;
            $fields->slug->disabled = true;
            $fields->is_active->disabled = true;
        }
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    /*  Scopes                                                                                                        */
    /* -------------------------------------------------------------------------------------------------------------- */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
