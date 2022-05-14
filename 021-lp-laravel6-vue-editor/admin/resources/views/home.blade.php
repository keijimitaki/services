@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!

                </div>
            </div>
            <button type="button" onclick="new function(){ location.href =  `./editor` ;}" class="btn-move btn btn-primary btn-lg">お知らせ編集</button>
            <!-- <button type="button" onclick="new function(){ location.href =  `{{ config('app.url') }}editor` ;}" class="btn-move btn btn-primary btn-lg">お知らせ編集</button> -->
        </div>
    </div>
</div>

@endsection

<script>
    
</script>