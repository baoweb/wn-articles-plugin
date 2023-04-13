<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateBaowebArticlesArticles5 extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->boolean('hide_in_lists')->default(0)->after('is_template');
        });
    }
    
    public function down()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->dropColumn('hide_in_lists');
        });
    }
}
