<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRelevantInXlsSurveyRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('xls_survey_rows', function (Blueprint $table) {
            $table->text('relevant')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('xls_survey_rows', function (Blueprint $table) {
            $table->string('relevant')->nullable()->change();
        });
    }
}
