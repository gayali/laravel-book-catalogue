<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class BookClient
{
    const SITE_URL = 'https://gutendex.com';
    public function fetchBooks($query = [])
    {
        $query = array_filter($query, function ($value) {
            return !is_null($value) && $value !== '';
        });
        $response = Http::get(self::SITE_URL . '/books', $query);
        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }
}
