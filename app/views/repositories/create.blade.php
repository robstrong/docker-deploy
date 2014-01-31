@extends('layouts.main')

@section('title')
Add Repository
@stop

@section('content')
    <div class="panel panel-primary">
        <div class="panel-heading">User Repositories</div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead></thead>
                <tbody>
                    @foreach ($repos as $repo)
                    <tr>
                        <td>{{{ $repo['full_name'] }}}</td>
                        <td>{{{ $repo['ssh_url'] }}}</td>
                        <td>
                            <form action="{{ action('RepositoriesController@store') }}" method="POST">
                                <input type="hidden" name="owner" value="{{{ $repo['owner']['login'] }}}">
                                <input type="hidden" name="repo" value="{{{ $repo['name'] }}}">
                                <button type="submit">Add</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">Organization Repositories</div>
        <div class="table-responsive">
            <table class="table">
                <thead></thead>
                <tbody>
                @foreach ($orgs as $org) 
                    <tr>
                        <td colspan="3"><h3>{{{ $org['login'] }}}</h3></td>
                    </tr>
                    @foreach ($org['repos'] as $repo)
                    <tr>
                        <td>{{{ $repo['name'] }}}</td>
                        <td>{{{ $repo['ssh_url'] }}}</td>
                        <td>
                            <form action="{{ action('RepositoriesController@store') }}" method="POST">
                                <input type="hidden" name="owner" value="{{{ $repo['owner']['login'] }}}">
                                <input type="hidden" name="repo" value="{{{ $repo['name'] }}}">
                                <button type="submit">Add</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
