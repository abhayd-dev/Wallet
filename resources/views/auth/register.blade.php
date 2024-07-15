@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <div class="col-sm-6 mx-auto p-3 "> 
    <h2 class="text-center mb-3">Register</h2>
    <hr class="border-danger"/>
    
    <form method="POST" action="{{ route('register') }}" class="form-control border-danger">
        @csrf
        <div class="form-group mt-3 mb-2">
            <label for="name">Name</label>
            <input type="text" class="form-control border-success mt-2" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
        </div>
        <div class="form-group mb-2">
            <label for="mobile">Mobile</label>
            <input type="text" class="form-control border-success mt-2" id="mobile" name="mobile" value="{{ old('mobile') }}" required autocomplete="mobile">
        </div>
        <div class="form-group mb-2">
            <label for="email">Email address</label>
            <input type="email" class="form-control border-success mt-2" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        </div>
        <div class="form-group mb-2">
            <label for="password">Password</label>
            <input type="password" class="form-control border-success mt-2" id="password" name="password" required autocomplete="new-password">
        </div>
        <div class="form-group mb-2">
            <label for="password-confirm">Confirm Password</label>
            <input type="password" class="form-control border-success mt-2" id="password-confirm" name="password_confirmation" required autocomplete="new-password">
        </div>
        <div class="form-group mt-4 text-center mb-4">
           <button type="submit" class="col-sm-6 btn btn-primary border-success mt-2">Register</button>
        </div>
       
    </form>
</div>
</div>
@endsection
