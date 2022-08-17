<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateBaowebArticlesArticles extends Migration
{
    public function up()
    {
        Schema::create('baoweb_articles_articles', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content')->nullable();
            $table->integer('created_by')->unsigned();
            $table->boolean('is_published')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('baoweb_articles_articles');
    }
}
