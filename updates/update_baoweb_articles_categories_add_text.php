<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class UpdateBaowebArticlesCategoriesAddText extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_categories', function($table)
        {
            $table->boolean('has_text')->default(0)->after('internal_name');
            $table->text('text')->nullable()->after('has_text');
        });
    }

    public function down()
    {
        Schema::table('baoweb_articles_categories', function($table)
        {
            $table->dropColumn('has_text');
            $table->dropColumn('text');
        });
    }
}