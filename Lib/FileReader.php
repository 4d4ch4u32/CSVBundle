<?php
/**
 * Created by PhpStorm.
 * User: adachauer
 * Date: 28.10.17
 * Time: 16:57
 */

namespace webworks\CSVBundle\Lib;

class FileReader
{
    const FILE_NOT_EXISTS = -1;
    const HANDLER_NOT_EXISTS = -2;
    const HANDLER_METHOD_NOT_EXISTS = -3;
    const FILE_READ_ERROR = -4;

    /**
     * @param $file
     * @param array $handler
     * @return int
     */
    public function readLineByLine($file, array $handler)
    {
        if (!file_exists($file)) {
            return self::FILE_NOT_EXISTS;
        }

        if (!isset($handler[0]) || !isset($handler[1])) {
            return self::HANDLER_NOT_EXISTS;
        }

        if (!method_exists($handler[0], $handler[1])) {
            return self::HANDLER_METHOD_NOT_EXISTS;
        }

        $handle = fopen($file, 'r');
        if ($handle) {
            $lineCount = 0;
            while (($line = fgets($handle)) !== false) {
                call_user_func($handler, [
                    'content' => $line,
                    'count' => $lineCount
                ]);
                $lineCount++;
            }

            fclose($handle);
        } else {
            return self::FILE_READ_ERROR;
        }
    }
}
