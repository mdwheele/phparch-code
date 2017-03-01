<?php

namespace Workflow\Providers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Workflow\Controllers\WorkflowController;

class RoutesProvider implements ControllerProviderInterface
{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $app[WorkflowController::class] = function() {
            return new WorkflowController();
        };

        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', WorkflowController::class . ':index');

        return $controllers;
    }
}