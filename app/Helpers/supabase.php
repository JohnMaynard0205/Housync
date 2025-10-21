<?php

use App\Services\SupabaseService;

if (!function_exists('supabase')) {
    /**
     * Get the Supabase service instance
     *
     * @return \App\Services\SupabaseService
     */
    function supabase()
    {
        return app(SupabaseService::class);
    }
}

