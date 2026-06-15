<?php

namespace App\Traits;


trait Instance
{

    public function setInstance(...$instance)
    {
        foreach ($instance as $key => $className) {
            if (class_exists($className)) {
                $bits = explode('\\', $className);
                $shortClassName = lcfirst(end($bits));
                $this->{$shortClassName} = app($className);
            }
        }
    }
}
