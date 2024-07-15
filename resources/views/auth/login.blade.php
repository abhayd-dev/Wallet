@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="col-sm-6 mx-auto p-3">
    <h2 class="text-center">Login</h2>
    <hr class="border-danger" />
    <form method="POST" action="{{ route('login') }}" class="form-control border-dark p-3">
        @csrf
        <div class="form-group mt-3 mb-3">
            <label for="email" class="mb-3">Email address</label>
            <input type="email" class="form-control border-success" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>
        <div class="form-group mb-3">
            <label for="password" class="mb-3">Password</label>
            <input type="password" class="form-control border-success" id="password" name="password" required autocomplete="current-password">
        </div>
        <div class="form-group text-center mb-3" >
        <button type="submit" class="btn btn-primary col-sm-6">Login</button>
    </div>
    </form>
</div>
</div>
@endsection
