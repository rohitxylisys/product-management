<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mode === 'edit' ? 'Edit Product' : ($mode === 'create' ? 'Add Product' : 'View Product') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include Quill Editor styles -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>{{ $mode === 'edit' ? 'Edit Product' : ($mode === 'create' ? 'Add Product' : 'View Product') }}</h2>
        @if ($mode === 'edit' || $mode === 'create')
        <form id="product-form" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
        @endif
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $product->title }}" {{ $mode === 'view' ? 'readonly' : '' }} required>
            </div>
            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input type="file" class="form-control-file" id="featured_image" name="featured_image" {{ $mode === 'view' ? 'disabled' : '' }} onchange="previewImage(event, 'featured_image_preview')">
                <br>
                <img id="featured_image_preview" src="{{ asset('storage/' . $product->featured_image) }}" alt="Featured Image Preview" style="max-width: 200px; height: auto;">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                @if ($mode === 'view')
                    <div>{!! $product->description !!}</div>
                @else
                    <div id="editor-container">{!! $product->description !!}</div>
                    <textarea name="description" id="description" style="display:none;">{!! $product->description !!}</textarea>
                @endif
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" {{ $mode === 'view' ? 'disabled' : '' }} required>
                    <option value="Active" {{ $product->status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ $product->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="gallery">Gallery</label>
                <input type="file" class="form-control-file" id="gallery" name="gallery[]" multiple {{ $mode === 'view' ? 'disabled' : '' }}>
                @if ($product->gallery)
                    <div class="row" id="gallery_preview">
                        @foreach (json_decode($product->gallery) as $image)
                            <div class="col-md-2">
                                <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                @if ($mode === 'edit' || $mode === 'create')
                                    <div class="text-center mt-2">
                                        <label class="text-danger">
                                            <input type="checkbox" name="delete_gallery[]" value="{{ $image }}"> Remove
                                        </label>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @if ($mode === 'edit' || $mode === 'create')
            <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            @endif
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Include Quill Editor scripts -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow'
        });

        document.getElementById('product-form').addEventListener('submit', function() {
            var description = document.querySelector('textarea[name=description]');
            description.value = quill.root.innerHTML;
        });

        function previewImage(event, previewId) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById(previewId);
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
