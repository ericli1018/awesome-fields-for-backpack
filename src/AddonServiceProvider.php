<?php

namespace Ericli1018\AwesomeFieldsForBackpack;

use Illuminate\Support\ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider;

    protected $vendorName = 'ericli1018';
    protected $packageName = 'awesome-fields-for-backpack';
    protected $commands = [];
}
