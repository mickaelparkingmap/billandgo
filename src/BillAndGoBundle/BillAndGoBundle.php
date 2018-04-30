<?php

namespace BillAndGoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BillAndGoBundle extends Bundle
{
    public function getParent() {
        return 'FOSUserBundle';
    }
}
