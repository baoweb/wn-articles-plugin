<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateBaowebArticlesCategories extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_categories', function($table)
        {
            $table->string('internal_name')->nullable()->after('name');
        });
    }
    
    public function down()
    {
        Schema::table('baoweb_articles_categories', function($table)
        {
            $table->dropColumn('internal_name');
        });
    }
}
