<?php

namespace Rtcl\Controllers;

class PublicAction
{
    public function __construct()
    {
        FormHandler::init();
        PageController::init();
        BusinessHoursController::init();
        SocialProfilesController::init();
    }
}
