@extends('layouts.main')

@section('title')
Repository - {{ $repo->repository }}
@stop

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        {{ Form::model($repo, array('method' => 'put', 'action' => array('RepositoriesController@update', $repo->id))) }}
        <dl>
            <dt>Config Override</dt>
            <dd>
                {{ Form::textarea('config_override') }}
            </dd>
            <br>
            <dd><button type="submit" class="btn btn-primary">Update</button></dd>
        </dl>
        {{ Form::close() }}
    </div>
</div>
@stop
