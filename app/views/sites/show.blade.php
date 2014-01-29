@extends('layouts.main')

@section('title')
Site - {{ $site->getFullUrl() }}
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <a class="btn btn-primary" href="/sites">Back</a>
        <a class="btn btn-primary" href="/sites/{{ $site->id }}/edit">Edit</a>
        {{ Form::open(
            array(
                'action' => array('SitesController@destroy', 'id' => $site->id),
                'class'  => 'display-inline',
                'method' => 'delete',
            )
        ) }}
        {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
        {{ Form::close() }}
    </div>
</div>
<br>
<div class="panel panel-default">
    <div class="panel-body">
        <dl>
            <dt>URL</dt>
            <dd><a href="http://{{ $site->getFullUrl() }}" target="_blank">{{ $site->getFullUrl() }}</a></dd>
            <dt>Repostory</dt>
            <dd>{{{ $site->repository->owner }}}/{{{ $site->repository->name }}}</dd>
            <dt>Branch</dt>
            <dd>{{ $site->branch }}</dd>
            <dt>Created</dt>
            <dd>{{ $site->created_at }}</dd>
            <dt>Updated</dt>
            <dd>{{ $site->updated_at }}</dd>
        </dl>
    </div>
</div>
@stop
