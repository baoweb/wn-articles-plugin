<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateBaowebArticlesArticles4 extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->smallInteger('is_published')->nullable(false)->unsigned()->default(0)->change();
        });
    }
    
    public function down()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->boolean('is_published')->nullable(false)->unsigned(false)->default(0)->change();
        });
    }
}
