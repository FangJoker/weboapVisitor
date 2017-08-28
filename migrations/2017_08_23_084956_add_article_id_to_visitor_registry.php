<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArticleIdToVisitorRegistry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(''visitor_registry'', function (Blueprint $table) {
            //设定字段'article_id'字段类型
            $table->integer('article_id')->unsigned()->index();
            //设定字段'article_id'为外来键，与content表中的content_id关联,联级删除
            $table->foreign('article_id')->references('content_id')->on('content')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(''visitor_registry'', function (Blueprint $table) {
            //
        });
    }
}
