<?php

// 从配置获取，不依赖sysinfo
function is_super($rid = null)
{
    $super = config('SUPER_ROLE');
    return $super && is_array($super) && in_array($rid, $super);
}
