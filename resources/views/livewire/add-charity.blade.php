@extends('layout.app')

@section('content')
<div>
<h4>Add a charity</h4>
<p>Search for a charity by name</p>

<form action="/charity/search" method="get" role="search">
            {{ csrf_field() }}
            <div class="input-group">
                <input type="text" class="form-control" name="q"
                    placeholder="Search ticker"> <span class="input-group-btn">
                    <button type="submit" class="btn btn-default">
                        <span class="glyphicon glyphicon-search">Search</span>
                    </button>
                </span>
            </div>
</form>
</div>
@endsection