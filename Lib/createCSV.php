<?php
/**
 * Created by PhpStorm.
 * User: adachauer
 * Date: 27.10.17
 * Time: 23:30
 */

namespace webworks\CSVBundle\Lib;

class createCSV
{

    /**
     * @param array $lines
     * @param string $delimiter
     * @param string $enclosure
     * @return string
     */
    public function arrayToCsv(array $lines, $delimiter = ';', $enclosure = '"')
    {
        $csvStr = '';

        $count = 0;
        foreach ($lines as $line) {
            if ($count == 0) {
                $cellNames = array_keys($line);
                foreach ($cellNames as $name) {
                    $csvStr .= $enclosure . $name . $enclosure . $delimiter;
                }
                $csvStr .= "\n\r";
            } else {
                foreach ($line as $cell) {
                    $csvStr .= $enclosure . $cell . $enclosure . $delimiter;
                }
                $csvStr .= "\n\r";
            }
            $count++;
        }

        return $csvStr;
    }
}
