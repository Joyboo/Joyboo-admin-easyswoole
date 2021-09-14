<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;
use Psr\Http\Message\UploadedFileInterface;

class MbLengthMin extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'MbLengthMin';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (is_numeric($itemData) || is_string($itemData)) {
            return mb_strlen((string)$itemData, mb_internal_encoding()) >= $arg;
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
