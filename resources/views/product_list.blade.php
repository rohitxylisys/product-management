<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-image {
            max-width: 100px;
            height: auto;
        }
        .categories {
            list-style: none;
            padding-left: 0;
        }
        .categories li {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            margin: 2px 0;
            border-radius: 3px;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="my-4 text-center">Product List</h1>
    <div class="mb-3">
    <a href="{{ route('products.create') }}" class="btn btn-success">Add Product</a>
</div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Featured Image</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Categories</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->title }}</td>
                        <td><img src="{{ $product->featured_image }}" class="product-image" alt="{{ $product->title }}"></td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->status }}</td>
                        <td>
                            <ul class="categories">
                                @foreach ($product->categories as $category)
                                    <li>{{ $category->title }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('products.edit', $product->slug) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('products.destroy', $product->slug) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
