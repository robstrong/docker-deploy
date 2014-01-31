<?php

class GithubController extends BaseController
{
    public function postHooks()
    {
        //rebuild sites that use this repo and are set to autoupdate
        //$payload = Input::json()->all();
        $payload = Input::get('payload');
        $payload = json_decode($payload, true);
        $repo = Repository::where('owner', '=', $payload['repository']['owner']['name'])
            ->where('name', '=', $payload['repository']['name'])->first();
        if ($repo) {
            $sites = Site::where('auto_update', '=', DB::raw('true'))->where('repository_id', '=', $repo->id)->get();
            foreach ($sites as $site) {
                $site->startInstance();
            }
        }
        return 'success';
    }
}
