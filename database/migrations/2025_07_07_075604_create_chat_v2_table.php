<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_tu_te_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('dify_conversation_id')->nullable();
            $table->string('title')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Thêm indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('last_message_at');
            $table->index(['user_id', 'status']); // Composite index cho queries phổ biến
            $table->index(['user_id', 'last_message_at']);
            $table->index('dify_conversation_id');
        });

        Schema::create('ai_tu_te_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_tu_te_conversations')->onDelete('cascade');
            $table->enum('sender', ['user', 'ai']);
            $table->longText('content');
            $table->text('query')->nullable();
            $table->string('path_image')->nullable();
            $table->json('metadata')->nullable(); // model AI, tokens, etc
            $table->boolean('is_processed')->default(false);
            $table->enum('is_hidden', ['yes', 'no'])->default('no');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('conversation_id');
            $table->index('sender');
            $table->index('is_processed');
            $table->index(['conversation_id', 'created_at']);
            $table->index(['conversation_id', 'sender']);
        });

        Schema::create('ai_tu_te_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('ai_tu_te_messages')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path'); // S3 path
            $table->string('file_type');
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->boolean('is_before_care')->default(false);
            $table->json('metadata')->nullable(); // For additional file info
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('message_id');
            $table->index('file_type');
            $table->index('is_processed');
            $table->index('is_before_care'); // Thêm index cho column mới
            $table->index(['message_id', 'file_type']);
            $table->index(['created_at', 'file_type']);
        });

        Schema::create('ai_tu_te_config_aff', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['standard', 'advanced'])->default('standard');
            $table->foreignId('config_aff_id')->constrained('config_aff')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('dify_dataset_id')->nullable();
            $table->string('dify_dataset_name')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_training_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('dify_document_id')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->string('name');
            $table->string('path');
            $table->longText('content');
            $table->string('fanpage_dify_app')->nullable();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('tone_config')->nullable();
            $table->string('dify_dataset_id')->nullable();
        });

        Schema::table('facebook_configs', function (Blueprint $table) {
            $table->string('fanpage_dify_app', 2)->default(1)->after('page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('ai_tu_te_messages', function (Blueprint $table) {
                $table->dropForeign('ai_tu_te_messages_conversation_id_foreign');
            });

            Schema::table('ai_tu_te_files', function (Blueprint $table) {
                $table->dropForeign('ai_tu_te_files_message_id_foreign');
            });

            Schema::table('user_sale', function (Blueprint $table) {
                $table->dropForeign('user_sale_conversation_id_foreign');
                $table->dropForeign('user_sale_message_id_foreign');
                $table->dropForeign('user_sale_user_id_foreign');
            });

            Schema::table('chat_training_documents', function (Blueprint $table) {
                $table->dropForeign('chat_training_documents_project_id_foreign');
                $table->dropColumn('project_id');
                $table->dropColumn('dify_document_id');
            });
        } catch (\Exception $e) {
        }

        Schema::dropIfExists('ai_tu_te_conversations');
        Schema::dropIfExists('ai_tu_te_messages');
        Schema::dropIfExists('ai_tu_te_files');
        Schema::dropIfExists('ai_tu_te_config_aff');
        Schema::dropIfExists('user_sale');
        Schema::dropIfExists('chat_training_documents');
        Schema::dropIfExists('projects');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tone_config');
            $table->dropColumn('dify_dataset_id');
        });
        Schema::table('facebook_configs', function (Blueprint $table) {
            $table->dropColumn('fanpage_dify_app');
        });
    }
};
