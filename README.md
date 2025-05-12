# Laravel Book Catalogue

This is a basic book catalogue project built with Laravel. It fetches book data from the [Gutendex API](https://gutendex.com/) and displays it on the `/books` URL. The project demonstrates the use of Laravel's Blade templating, controller logic, and separation of API client logic into a dedicated file.

---

## Features

-   Fetches book data from the Gutendex API.
-   Displays books with details like title, author, language, subjects, and summaries.
-   Allows filtering by language, sorting (ascending, descending, popular), and searching by title.
-   Pagination for navigating through the book list.
-   Responsive UI built with Bootstrap 5.
-   Demonstrates Laravel best practices:
    -   Blade templating.
    -   Separation of concerns by moving API logic to a dedicated client file.
    -   Clean controller logic.

---

## Installation

Follow these steps to set up the project locally:

### Prerequisites

-   PHP >= 8.1
-   Composer
-   Laravel >= 10.x
-   A web server (e.g., Apache, Nginx, or Laravel's built-in server)
-   Node.js and npm (optional, for frontend asset compilation)

### Steps

1. **Clone the Repository**:

    ```bash
    git clone https://github.com/gayali/laravel-book-catalogue.git
    cd laravel-book-catalogue
    ```

2. **Install Dependencies**: Run the following command to install PHP dependencies:

    ```bash
    composer install
    ```

3. **Set Up Environment**: Copy the .env.example file to .env:

    ```bash
    cp .env.example .env
    ```

    Update the .env file with your application settings (e.g., database connection, app URL).

4. **Generate Application Key**:

    ```bash
    php artisan key:generate
    ```

5. **Run the Application**: Start the Laravel development server:

    ```bash
    php artisan serve
    ```

    The application will be available at http://127.0.0.1:8000

---

## Usage

### Accessing the Application

-   Navigate to `/books` to view the book catalogue.
-   Example URL: `http://127.0.0.1:8000/books`.

### Features

1. **Search**:

    - Use the search bar to find books by title.

2. **Filter by Language**:

    - Click the "Filters" button to open the left panel.
    - Select one or more languages (e.g., English, French) and click "Apply Filter".

3. **Sort**:

    - Use the "Sort By" dropdown to sort books by:
        - Ascending
        - Descending
        - Popular

4. **Pagination**:

    - Use the "Previous Page" and "Next Page" buttons to navigate through the book list.

5. **View Details**:
    - Click the "View Details" button on any book card to open a modal with detailed information about the book.

---

## Project Structure

### Routes

The routes are defined in `routes/web.php`:

```php
Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::get('/books', [BookController::class, 'index'])->name('books.index');
```

---

### Controllers

The main logic is handled in `BookController.php`:

```php
// filepath: app/Http/Controllers/BookController.php
public function index(Request $request)
{
    $validated = $request->validate([
        'search' => 'nullable|string|max:255',
        'page' => 'nullable|string|min:1',
        'languages' => 'nullable|array',
        'languages.*' => 'string|size:2|regex:/^[a-z]{2}$/',
        'sort' => 'nullable|string|in:ascending,descending,popular',
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

        return view('books.index', compact('books'));
    } catch (Exception $e) {
        Log::error($e);
        return response(['error' => 'Something went wrong, please try again later'], 500);
    }
}
```

---

### API Client

The API logic is separated into `BookClient.php`:

```php
// filepath: app/Clients/BookClient.php
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
```

-   **Purpose**: Handles API requests to the Gutendex API.
-   **Method**: `fetchBooks($query)` fetches books based on the query parameters.

---

### Blade Templates

The frontend is built using Blade templates. Key files:

1. **Layout**:

    - `resources/views/layouts/app.blade.php`: Contains the base HTML structure and includes Bootstrap.

2. **Books Index**:
    - `resources/views/books/index.blade.php`: Displays the book catalogue with filters, sorting, and pagination.

---

### Frontend

The UI is built with Bootstrap 5 (via CDN). Key features:

1. **Navbar**:

    - Displays the application title.

2. **Filters**:

    - A left panel (offcanvas) for filtering by language.

3. **Sorting**:

    - A dropdown for sorting books.

4. **Pagination**:

    - Buttons for navigating between pages.

5. **Responsive Design**:
    - The layout is fully responsive and works on all screen sizes.

---

## Example API Query

The application sends requests to the Gutendex API with query parameters like:

```http
GET https://gutendex.com/books?search=frankenstein&languages=en,fr&sort=popular&page=2
```

---

## Customization

1. **Add More Filters**:

    - Update the `BookController` and `index.blade.php` to include additional filters (e.g., subjects, authors).

2. **Styling**:

    - Modify `app.blade.php` to include custom CSS or use a different frontend framework.

3. **Extend API Logic**:
    - Add more methods to `BookClient.php` for fetching specific book details or other endpoints.

---

## Troubleshooting

1. **No Books Found**:

    - Ensure the Gutendex API is reachable.
    - Check the query parameters being sent.

2. **Styling Issues**:

    - Ensure the Bootstrap CDN is loaded correctly.

3. **Validation Errors**:
    - Check the validation rules in `BookController.php`.

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
