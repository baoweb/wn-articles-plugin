<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateBaowebArticlesCategories extends Migration
{
    public function up()
    {
        Schema::create('baoweb_articles_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(0);
            $table->string('language', 2)->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->string('default_order')->nullable();
            $table->string('default_order_direction')->nullable();
            $table->smallInteger('default_items')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('baoweb_articles_categories');
    }
}
