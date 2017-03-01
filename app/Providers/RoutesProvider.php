<?php

namespace Sample\Providers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Sample\Controllers\WorkflowController;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

        $controllers->get('/', function() use ($app) {
            return new RedirectResponse($app->url('workflow.basic'));
        });

        $controllers->get('/workflows/basic', WorkflowController::class . ':showBasic')->bind('workflow.basic');
        $controllers->get('/workflows/user/basic', WorkflowController::class . ':showUserBasic')->bind('workflow.user.basic');
        $controllers->get('/workflows/user/complex', WorkflowController::class . ':showUserComplex')->bind('workflow.user.complex');

        $controllers->get('/vue', WorkflowController::class . ':showVueApp')->bind('vuejs');

        $controllers->get('/api/simulation/basic', WorkflowController::class . ':showBasicSimulationJson');
        $controllers->get('/api/simulation/complex', WorkflowController::class . ':showComplexSimulationJson');

        return $controllers;
    }
}