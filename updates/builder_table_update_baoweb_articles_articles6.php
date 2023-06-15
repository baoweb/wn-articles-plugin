<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateBaowebArticlesArticles6 extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->boolean('is_news_item')->default(0)->after('is_published');
            $table->integer('primary_category')->nullable()->unsigned()->after('is_news_item');
        });
    }
    
    public function down()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->dropColumn('is_news_item');
            $table->dropColumn('primary_category');
        });
    }
}