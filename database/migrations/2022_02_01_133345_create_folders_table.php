<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (config('creator.app_use_media')) {

            Schema::create('folders', function (Blueprint $table) {

                $table->id();
                $table->string('name')->nullable();
                $table->foreignId('parent_id')->nullable()->constrained('folders');

                $table->timestamps();
            });
            DB::table('folders')->insert([
                'name' => 'Documentos'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('folders');
    }
};
