<?php namespace Baoweb\Articles\Models;

use Backend\Models\User;
use Model;
use Backend\Facades\BackendAuth;

/**
 * Model
 */
class Article extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    use \Winter\Storm\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    protected $jsonable = ['content'];

    public $belongsTo = [
        'author' => [
            User::class,
            'key' => 'created_by'
        ]
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
}
