<?php

namespace Blazon\Plugin;

use Twig\Environment;

class TwigInitEvent
{
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
