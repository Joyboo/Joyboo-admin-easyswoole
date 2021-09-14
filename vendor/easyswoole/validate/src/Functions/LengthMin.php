<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;
use Psr\Http\Message\UploadedFileInterface;

class LengthMin extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'LengthMin';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (is_numeric($itemData) || is_string($itemData)) {
            return strlen($itemData) >= $arg;
        }
        if (is_array($itemData) && (count($itemData) >= $arg)) {
            return true;
        }
        if (($itemData instanceof UploadedFileInterface) && ($itemData->getSize() >= $arg)) {
            return true;
        }

        return false;
    }
}
