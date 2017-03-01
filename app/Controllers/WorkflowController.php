<?php

namespace Workflow\Controllers;

use Silex\Application;

class WorkflowController
{
    public function index(Application $app)
    {
        return $app->render('master.twig');
    }
}