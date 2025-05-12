@extends('layouts.app')
@section('content')
    @php
        // Mapping of language codes to full names
        $languageNames = [
            'en' => 'English',
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
        ];
    @endphp
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-dark " data-bs-theme="dark">
        <div class="container ">
            <div class="row w-100 align-items-center mx-auto">
                <div class="col-lg-4">
                    <a class="navbar-brand" href="#"><i class="bi bi-book"></i>
                        Books Catalogue
                    </a>
                </div>
                <div class="col-lg-8 text-end">
                    <div class="input-group my-1 ">
                        <input type="text" name="search" placeholder="Search any book..." aria-label="Search"
                            value="{{ request('search') }}" class="form-control" form="filter-form" />

                        @if (request()->hasAny(['search', 'languages', 'sort']))
                            <span class="input-group-text" title="Clear Filter"><a href="{{ route('books.index') }}"
                                    class="btn-close input-group-text" aria-label="Close"></a></span>
                        @endif

                        <button type="submit" form="filter-form" class="btn btn-warning"> <i class="bi bi-search"></i>
                            Search</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="bg-white border-bottom" style="box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
        <div class="container">
            <form method="GET" class="row py-2 justify-content-between align-items-center" id="filter-form">
                {{ @csrf_field() }}
                <div class="col-lg-2">
                    <!-- Left Panel Toggle Button -->
                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#leftPanel" aria-controls="leftPanel">
                        <i class="bi bi-filter"></i> Filters
                    </button>
                </div>

                <div class="col-lg-8 text-center">
                    @if ($books['count'] > 0)
                        <small>showing results over {{ $books['count'] }} books</small>
                        @if (request()->has('search'))
                            <small class="text-muted">for
                                <strong>{{ request('search') }}</strong></small>
                        @endif
                    @endif
                </div>

                <div class="col-lg-2">
                    <div class="input-group my-1">
                        <span class="input-group-text"><i class="bi bi-sort-down"></i></span>
                        <select name="sort" class="form-select form-select-sm form-select-dark"
                            onchange="this.form.submit()">
                            <option hidden value="">Sort By</option>
                            <option value="ascending" {{ request('sort') == 'ascending' ? 'selected' : '' }}>Ascending
                            </option>
                            <option value="descending" {{ request('sort') == 'descending' ? 'selected' : '' }}>
                                Descending
                            </option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular
                            </option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Left Panel (Offcanvas) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="leftPanel" aria-labelledby="leftPanelLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="leftPanelLabel">Filter by Language</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-3">
                <label for="languages" class="form-label">Select Languages</label>
                <select name="languages[]" id="languages" class="form-select" multiple form="filter-form">
                    <option value="en" {{ in_array('en', request('languages', [])) ? 'selected' : '' }}>English
                    </option>
                    <option value="fr" {{ in_array('fr', request('languages', [])) ? 'selected' : '' }}>French
                    </option>
                    <option value="es" {{ in_array('es', request('languages', [])) ? 'selected' : '' }}>Spanish
                    </option>
                    <option value="de" {{ in_array('de', request('languages', [])) ? 'selected' : '' }}>German
                    </option>
                    <option value="it" {{ in_array('it', request('languages', [])) ? 'selected' : '' }}>Italian
                    </option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary" form="filter-form">Apply Filter</button>
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Clear Filter</a>
            </div>

        </div>
    </div>

    <div class="container mt-4 mb-5">


        @if (count($books['results']) > 0)
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                @foreach ($books['results'] as $book)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ $book['title'] }}</h5>
                                <p class="card-text">
                                    <strong>Author:</strong> {{ $book['authors'][0]['name'] ?? 'Unknown Author' }}
                                    ({{ $book['authors'][0]['birth_year'] ?? 'N/A' }} -
                                    {{ $book['authors'][0]['death_year'] ?? 'N/A' }})
                                </p>
                                <p class="card-text">
                                    <strong>Language:</strong>
                                    @if (!empty($book['languages']))
                                        {{ implode(', ', array_map(fn($lang) => $languageNames[$lang] ?? ucfirst($lang), $book['languages'])) }}
                                    @else
                                        Unknown
                                    @endif
                                </p>
                                <p class="card-text">
                                    <strong>Subjects:</strong>
                                    @foreach ($book['subjects'] ?? [] as $subject)
                                        <span class="badge bg-secondary my-1"
                                            style="white-space: normal;">{{ $subject }}</span>
                                    @endforeach
                                </p>

                                @isset($book['summaries'][0])
                                    <p class="card-text">
                                        <strong>Summary:</strong>
                                        {{ Str::limit($book['summaries'][0] ?? 'No summary available.', 100) }}
                                    </p>
                                @endisset

                            </div>
                            <div class="card-footer text-end">
                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#bookModal{{ $book['id'] }}">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="bookModal{{ $book['id'] }}" tabindex="-1"
                        aria-labelledby="bookModalLabel{{ $book['id'] }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bookModalLabel{{ $book['id'] }}">
                                        {{ $book['title'] }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="{{ $book['formats']['image/jpeg'] ?? 'https://via.placeholder.com/150' }}"
                                        class="img-fluid mb-3" alt="{{ $book['title'] }}">
                                    <p><strong>Author:</strong> {{ $book['authors'][0]['name'] ?? 'Unknown Author' }}
                                        ({{ $book['authors'][0]['birth_year'] ?? 'N/A' }} -
                                        {{ $book['authors'][0]['death_year'] ?? 'N/A' }})
                                    </p>
                                    <p><strong>Language:</strong>
                                        @if (!empty($book['languages']))
                                            {{ implode(', ', array_map(fn($lang) => $languageNames[$lang] ?? ucfirst($lang), $book['languages'])) }}
                                        @else
                                            Unknown
                                        @endif

                                    </p>
                                    <p><strong>Subjects:</strong>
                                        @foreach ($book['subjects'] ?? [] as $subject)
                                            <span class="badge bg-secondary">{{ $subject }}</span>
                                        @endforeach
                                    </p>
                                    <p><strong>Bookshelves:</strong>
                                        @foreach ($book['bookshelves'] ?? [] as $bookshelf)
                                            <span class="badge bg-info text-dark">{{ $bookshelf }}</span>
                                        @endforeach
                                    </p>
                                    <p><strong>Summary:</strong> {{ $book['summaries'][0] ?? 'No summary available.' }}
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center mt-5">
                <p class="text-muted fs-1">
                    <i class="bi bi-emoji-frown" style="font-size: 4rem;"></i>
                </p>
                <h3 class="text-muted">Oops! No books found</h3>
                <p class="text-muted">Try adjusting your filters or search for something else.</p>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-4 pt-4 text-center">
            <div class="d-flex justify-content-center gap-3">
                @if (request()->has('page') && request('page') > 1)
                    <a href="?{{ http_build_query(array_merge(request()->all(), ['page' => request('page') - 1])) }}"
                        class="btn btn-secondary">
                        ← Previous Page
                    </a>
                @endif

                @if ($books['nextPageParams'])
                    <a href="?{{ $books['nextPageParams'] }}" class="btn btn-primary">
                        Next Page →
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
