<?php

namespace Blazon\Plugin;

use Blazon\Model\Publication;
use Psr\Container\ContainerInterface;

abstract class BasePlugin
{

    public function getName(): string
    {
        return get_called_class();
    }

}
