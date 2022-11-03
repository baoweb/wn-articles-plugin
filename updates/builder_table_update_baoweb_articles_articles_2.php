<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateBaowebArticlesArticles2 extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->string('language')->nullable()->after('is_template');
        });
    }
    
    public function down()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->dropColumn('language');
        });
    }
}
