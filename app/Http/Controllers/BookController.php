<?php

namespace App\Http\Controllers;

use App\Clients\BookClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|string|min:1',
            'languages' => 'nullable|array',
            'languages.*' => 'string|size:2|regex:/^[a-z]{2}$/',
            'sort' => 'nullable|string|in:ascending,descending,popular',
            'next' => 'nullable|string|max:255',
            'previous' => 'nullable|string|max:255'
        ]);

        try {

            if (isset($validated['languages']) && is_array($validated['languages'])) {
                $validated['languages'] = implode(',', $validated['languages']);
            }

            $books = (new BookClient)->fetchBooks($validated);
            if (!$books) {
                Log::error('Error fetching books: Books not found');
                return response(['error' => 'Books not found'], 404);
            }

            if ($books['next'] !== null) {
                $validated['page'] = isset($validated['page']) ? $validated['page'] + 1 : 2;
                $books['nextPageParams'] = http_build_query($validated);
            } else {
                $books['nextPageParams'] = null;
            }


            return view('books.index', compact('books'));
        } catch (Exception $e) {
            Log::error($e);
            return response(['error' => 'Something went wrong, please try again later'], 500);
        }
    }
}
