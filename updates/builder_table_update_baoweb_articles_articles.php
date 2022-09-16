<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateBaowebArticlesArticles extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->boolean('is_template')->default(0)->after('is_featured');
            $table->boolean('is_scheduled')->default(0)->after('published_at');
            $table->dateTime('publish_at')->nullable()->after('is_scheduled');
            $table->dateTime('unpublish_at')->nullable()->after('publish_at');
        });
    }
    
    public function down()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->dropColumn('publish_at');
            $table->dropColumn('unpublish_at');
            $table->dropColumn('is_scheduled');
            $table->dropColumn('is_template');
        });
    }
}
