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

        Schema::create('spply_matching', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('matching_id');
			$table->string('target_module', 255);
			$table->string('target_id', 255);
			$table->string('target_code', 255);
			$table->text('target_content');
			$table->string('source_provider', 255);
			$table->string('source_id', 255);
			$table->string('source_code', 255);
			$table->text('source_content');
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
        Schema::dropIfExists('spply_matching');
    }

};
