<?php

namespace Strong\Deploy\Queue;

class BuildImage
{
    public function fire($job, $data)
    {
        $site = \Site::findOrFail($data['site_id']);
        $site->buildImage();
        if (!empty($data['start_container'])) {
            $site->startContainer();
        }

        $job->delete();
    }
}
