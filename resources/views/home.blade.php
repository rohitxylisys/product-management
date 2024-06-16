@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('User Details') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mt-2">
                        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                        @if (Auth::user()->hasRole('admin'))

                        <p><strong>Roles:</strong>
                            @foreach (Auth::user()->roles as $role)
                                {{ $role->name }}
                                @if (!$loop->last), @endif
                            @endforeach
                        </p>

                        <p><strong>Permissions:</strong>
                            @foreach (Auth::user()->getAllPermissions() as $permission)
                                {{ $permission->name }}
                                @if (!$loop->last), @endif
                            @endforeach
                        </p>
                        @endif

                        <!-- Add more user information as needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
