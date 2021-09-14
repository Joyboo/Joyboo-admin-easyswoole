<?php

namespace EasySwoole\Validate\Functions;

use EasySwoole\Validate\Validate;
use Psr\Http\Message\UploadedFileInterface;

class AllowFile extends AbstractValidateFunction
{
    public function name(): string
    {
        return 'AllowFile';
    }

    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        if (!$itemData instanceof UploadedFileInterface) {
            return false;
        }

        $array = array_shift($arg);
        $isStrict = array_shift($arg);

        $filename = $itemData->getClientFilename();
        if (!$filename) {
            return false;
        }

        $extension = pathinfo($filename)['extension'] ?? '';

        if (!in_array($extension, $array, $isStrict)) {
            return false;
        }

        return true;
    }
}
