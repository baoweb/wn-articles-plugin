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

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'baoweb_articles_categories';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    protected static function booted()
    {
        static::addGlobalScope('onlyAllowed', function (Builder $builder) {

            $user = BackendAuth::getUser();

            if(!App::runningInBackend()) {
                return;
            }

            if($user->hasAccess('baoweb.articles.edit-all-articles')) {
                return;
            }

            $categories = DB::table('baoweb_articles_users_categories')
                ->where('user_id', $user->id)
                ->pluck('category_id');

            $builder->whereIn('baoweb_articles_categories.id', $categories);
        });
    }
}
