<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageFeaturesTable extends Migration
{

    public function up()
    {
        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('config_aff_id'); // ID của gói (package)
            $table->string('feature_code'); // Mã chức năng
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('config_aff_id')->references('id')->on('config_aff')
                ->onDelete('cascade'); // Xóa gói thì các chức năng cũng bị xóa

            $table->index(['config_aff_id', 'feature_code'], 'package_features_composite_index');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_features');
    }
};
