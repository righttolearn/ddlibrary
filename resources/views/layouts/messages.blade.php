@if(isset($errors) && $errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-warning" role="alert">
            {{ $error }}
        </div>
    @endforeach
@endif

@if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-warning" role="alert">
        {{ session('error') }}
    </div>
@endif
