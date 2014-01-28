<?php

class SitesController extends \BaseController
{
    public function index()
    {
        return View::make(
            'sites.index',
            array(
                'sites' => Site::with('repository', 'domain')->orderBy('subdomain')->get()
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

        $domains = Domain::orderBy('domain')->get();
        $domainDropdown = array();
        foreach ($domains as $domain) {
            $domainDropdown[$domain->id] = $domain->domain;
        }

        return View::make(
            'sites.create',
            array(
                'repoDropdown'      => $repoDropdown,
                'domainDropdown'    => $domainDropdown,
            )
        );
    }

    public function store()
    {
        $rules = array(
            'subdomain'     => 'alpha_dash',
            'domain_id'     => 'required|integer|exists:domains,id',
            'repository'    => 'required',
            'branch'        => 'required|alpha_dash',
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
        $site->domain_id = Input::get('domain_id');
        $site->subdomain = Input::get('subdomain');
        $site->branch = Input::get('branch');
        $site->save();

        Session::flash('success', 'Site successfully created');
        return Redirect::action('SitesController@index');
    }

    public function show($id)
    {
        $site = Site::findOrFail($id);
        return View::make(
            'sites.show',
            array(
                'site' => $site
            )
        );
    }

    public function destroy($id)
    {
        $site = Site::findOrFail($id);
        $site->delete();
        Session::flash('success', 'Site successfully deleted');
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
