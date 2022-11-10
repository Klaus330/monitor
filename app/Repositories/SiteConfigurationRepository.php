<?php

namespace App\Repositories;

use App\Models\SiteConfiguration;

class SiteConfigurationRepository
{
    public function create(array $data): SiteConfiguration
    {
        return SiteConfiguration::create($data);
    }

    public function update(array $data)
    {
        $config = SiteConfiguration::find($data['site_id']);
        unset($data['site_id']);

        return $config->update($data);
    }
}
