<?php

if (! function_exists('deep_copy'))
{
    function deep_copy($object) {
        return (new DeepCopy\DeepCopy())->copy($object);
    }
}
