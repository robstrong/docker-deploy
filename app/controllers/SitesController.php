<?php

class SitesController extends \BaseController
{
    public function index()
    {
        return View::make(
            'sites.index',
            array(
                'sites' => Site::with('repository')->orderBy('url')->get()
            )
        );
    }

    public function create()
    {
        $github = App::make('github');

        $repoDropdown = array('User Repositories' => array());
        $userRepos = $github->api('current_user')->repositories();
        foreach ($userRepos as $repo) {
            $repoDropdown['User Repositories'][$repo['owner']['login'] . '/' . $repo['name']] = $repo['owner']['login'] . '/' . $repo['name'];
        }

        $orgs = $github->api('current_user')->organizations();
        foreach ($orgs as &$org) {
            $repos = $github->api('organization')->repositories($org['login']);
            foreach ($repos as $repo) {
                $repoDropdown[$org['login']][$repo['owner']['login'] . '/' . $repo['name']] = $repo['owner']['login'] . '/' . $repo['name'];
            }
        }

        return View::make(
            'sites.create',
            array(
                'repoDropdown'  => $repoDropdown,
            )
        );
    }

    public function store()
    {
        $rules = array(
            'url'           => 'required',
            'repository'    => 'required',
            'branch'        => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::action('SitesController@create')->withErrors($validator);
        }

        //find repository or create if not exists
        $repoParts = explode('/', Input::get('repository'));
        $owner = $repoParts[0];
        $repo = $repoParts[1];
        try {
            $repoModel = Repository::where('owner', '=', $owner)->where('name', '=', $repo)->firstOrFail();
        } catch (Exception $e) {
            $repoModel = new Repository;
            $repoModel->owner = $owner;
            $repoModel->name = $repo;
            $repoModel->auth_user_id = Auth::user()->id;
            $repoModel->save();
        }
        
        $site = new Site;
        $site->repository_id = $repoModel->id;
        $site->url = Input::get('url');
        $site->branch = Input::get('branch');
        $site->save();

        Session::flash('success', 'Site successfully created');
        return Redirect::action('SitesController@index');
    }

    public function startInstance($id)
    {
        $site = Site::findOrFail($id);
        $site->startContainer();
        Session::flash('success', 'Started Container');
        return Redirect::action('SitesController@index');
    }
}
