<?php

function isSuper($rid = null)
{
    $super = config('SUPER_ROLE');
    return in_array($rid, $super);
}
