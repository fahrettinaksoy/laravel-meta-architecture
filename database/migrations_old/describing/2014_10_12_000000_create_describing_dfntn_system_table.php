<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('describing')->create('dfntn_systm_component', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('component_id');
			$table->string('parent_code', 255);
			$table->string('operation', 255);
			$table->string('code', 255);
			$table->string('slug', 255);
			$table->text('education_video');
			$table->string('recurring_type_id', 255);
			$table->string('icon', 255);
			$table->string('controller', 255);
			$table->text('method');
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('dfntn_systm_component');

    }

};
