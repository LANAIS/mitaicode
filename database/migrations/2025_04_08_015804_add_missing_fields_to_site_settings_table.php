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
        Schema::table('site_settings', function (Blueprint $table) {
            // Testimonios
            if (!Schema::hasColumn('site_settings', 'testimonial1_content')) {
                $table->text('testimonial1_content')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial1_author')) {
                $table->string('testimonial1_author')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial1_role')) {
                $table->string('testimonial1_role')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial2_content')) {
                $table->text('testimonial2_content')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial2_author')) {
                $table->string('testimonial2_author')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial2_role')) {
                $table->string('testimonial2_role')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial3_content')) {
                $table->text('testimonial3_content')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial3_author')) {
                $table->string('testimonial3_author')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'testimonial3_role')) {
                $table->string('testimonial3_role')->nullable();
            }
            
            // Sección de Registro
            if (!Schema::hasColumn('site_settings', 'register_title')) {
                $table->string('register_title')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'register_subtitle')) {
                $table->text('register_subtitle')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'student_label')) {
                $table->string('student_label')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'teacher_label')) {
                $table->string('teacher_label')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'register_button_text')) {
                $table->string('register_button_text')->nullable();
            }
            
            // Footer
            if (!Schema::hasColumn('site_settings', 'footer_description')) {
                $table->text('footer_description')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'footer_copyright')) {
                $table->string('footer_copyright')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Eliminar campos de Testimonios
            $table->dropColumn([
                'testimonial1_content',
                'testimonial1_author',
                'testimonial1_role',
                'testimonial2_content',
                'testimonial2_author',
                'testimonial2_role',
                'testimonial3_content',
                'testimonial3_author',
                'testimonial3_role',
            ]);
            
            // Eliminar campos de Sección de Registro
            $table->dropColumn([
                'register_title',
                'register_subtitle',
                'student_label',
                'teacher_label',
                'register_button_text',
            ]);
            
            // Eliminar campos de Footer
            $table->dropColumn([
                'footer_description',
                'contact_email',
                'contact_phone',
                'footer_copyright',
            ]);
        });
    }
};
