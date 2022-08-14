<?php namespace Baoweb\Articles\Models;

use Model;

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
}
