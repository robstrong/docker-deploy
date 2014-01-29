<?php

namespace Strong\Deploy\Queue;

class CreateInstance
{
    public function fire($job, $data)
    {
        $site = \Site::findOrFail($data['site_id']);
        $site->startInstance();

        $job->delete();
    }
}
