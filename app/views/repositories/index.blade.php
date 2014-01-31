@extends('layouts.main')

@section('title')
Repositories
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="btn-group">
          <a class="btn btn-primary" href="/repositories/create">Add</a>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Repository</th>
                            <th>Github User</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($repos as $repo)
                        <tr>
                            <td><a href="/repositories/{{ $repo->id }}">{{ $repo->repository }}</a></td>
                            <td>{{ isset($repo->user) ? $repo->user->github_login : '' }}</td>
                            <td>{{ $repo->created_at }}</td>
                            <td>{{ $repo->updated_at }}</td>
                            <td><a class="btn btn-primary" href="{{ URL::action('RepositoriesController@installWebhooks', array('id' => $repo->id)) }}">Setup Webhooks</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
