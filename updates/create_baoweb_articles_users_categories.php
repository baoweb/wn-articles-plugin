<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateBaowebArticlesUsersCategories extends Migration
{
    public function up()
    {
        Schema::create('baoweb_articles_users_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->foreignId('user_id');
            $table->foreignId('category_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('baoweb_articles_users_categories');
    }
}