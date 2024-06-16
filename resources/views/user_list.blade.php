
@extends('layouts.app')
@section('content')<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin: 20px 0;
        }
        .permissions {
            list-style: none;
            padding-left: 0;
        }
        .permissions li {
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
    <h1 class="my-4 text-center">User List</h1>
    <div class="row">
        @foreach ($users as $user)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $user['name'] }}</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Role:</strong> {{ $user['role'] ?? 'No Role Assigned' }}</p>
                        <p><strong>Permissions:</strong></p>
                        <ul class="permissions">
                            @forelse ($user['permissions'] as $permission)
                                <li>{{ $permission }}</li>
                            @empty
                                <li>No Permissions</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
@endsection