<?php namespace Baoweb\Articles\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class AddAuthorField extends Migration
{
    public function up()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->string('custom_author')->nullable()->after('created_by');
        });
    }

    public function down()
    {
        Schema::table('baoweb_articles_articles', function($table)
        {
            $table->dropColumn('custom_author');
        });
    }
}