<?php

namespace Sample;

use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;

class Application extends \Silex\Application
{
    use TwigTrait;
    use UrlGeneratorTrait;
}