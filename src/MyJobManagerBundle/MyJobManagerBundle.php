<?php

namespace MyJobManagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MyJobManagerBundle extends Bundle
{
    public function getParent() {
        return 'FOSUserBundle';
    }
}