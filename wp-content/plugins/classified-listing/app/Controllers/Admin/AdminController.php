<?php

namespace Rtcl\Controllers\Admin;


use Rtcl\Controllers\Admin\Meta\MetaController;
use Rtcl\Controllers\Settings\AdminSettings;

class AdminController
{

    public function __construct() {
        new AddConfig();
        new PaymentStatus();
        ListingStatus::init();
        RegisterPostType::init();
        new MetaController();
        new ScriptLoader();
        new AdminSettings();
        new Cron();
        new EmailSettings();
    }

}
