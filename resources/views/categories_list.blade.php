@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Categories</h2>
    @if(Auth::user()->isAdmin())
    <a href="{{ url(route('categories.add')) }}" class="btn btn-success mb-3">Add Category</a>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                @if(Auth::user()->isAdmin())
                <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td><a href="#" class="category-link" data-category-id="{{ $category->id }}">{{ $category->title }}</a></td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->status }}</td>
                @if(Auth::user()->isAdmin())
                <td>
                    <a href="{{ route('categories.edit', $category->slug) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="card mb-4">
        <div class="card-header">
            <h4 id="category-product-heading" class="m-0">Products in Selected Category</h4>
        </div>
        <div id="category-product-list" class="card-body">
            <!-- Product list will be dynamically populated here -->
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle click on category link
        $('.category-link').click(function(event) {
            event.preventDefault();
            var categoryId = $(this).data('category-id');

            // AJAX request to fetch products by category ID
            $.ajax({
                url: '{{ route('categories.products') }}', // Route to fetch products by category
                type: 'GET',
                data: { category_id: categoryId },
                success: function(response) {
                    // Update the page with the fetched products data
                    if (response.length > 0) {
                        var html = '<ul class="list-group">';
                        $.each(response, function(index, product) {
                            html += '<li class="list-group-item">' + product.title + '</li>';
                        });
                        html += '</ul>';
                        $('#category-product-list').html(html);
                    } else {
                        $('#category-product-list').html('<p>No products found in this category.</p>');
                    }
                    $('#category-product-heading').text('Products in Category: ' + response[0].categories[0].title);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log error message
                    alert('Failed to fetch products for the category.');
                }
            });
        });
    });
</script>
@endsection
