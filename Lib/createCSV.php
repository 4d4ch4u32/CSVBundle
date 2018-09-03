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
        foreach ($lines as $line) {
            if ( strlen( $csvStr ) < 1 ) {
                $cellNames = array_keys($line);
                foreach ($cellNames as $name) {
                    $csvStr .= $enclosure . $name . $enclosure . $delimiter;
                }
                $csvStr .= "\n\r";
            }
            
            foreach ($line as $cell) {
                $csvStr .= $enclosure . $cell . $enclosure . $delimiter;
            }
            $csvStr .= "\n\r";
        }

        return $csvStr;
    }
}
