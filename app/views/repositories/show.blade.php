@extends('layouts.main')

@section('title')
Repository - {{ $repo->repository }}
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <a class="btn btn-primary" href="/repositories">Back</a>
        <a class="btn btn-primary" href="/repositories/{{ $repo->id }}/edit">Edit</a>
    </div>
</div>
<br>
<div class="panel panel-default">
    <div class="panel-body">
        <dl>
            <dt>Github User</dt>
            <dd>{{ $repo->user->github_login }}</dd>
            <br>
            <dt>Created</dt>
            <dd>{{ $repo->created_at }}</dd>
            <br>
            <dt>Updated</dt>
            <dd>{{ $repo->updated_at }}</dd>
            <br>
            <dt>Config Override</dt>
            <dd>
                <pre>{{ $repo->config_override }}</pre>
            </dd>
        </dl>
    </div>
</div>
@stop
