<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;
use Psr\Http\Message\UploadedFileInterface;

class AllowFileType extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'AllowFileType';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!$itemData instanceof UploadedFileInterface) {
            return false;
        }

        $array = array_shift($arg);
        $isStrict = array_shift($arg);

        if (!in_array($itemData->getClientMediaType(), $array, $isStrict)) {
            return false;
        }

        return true;
    }
}
