<?php
/**
 * Created by PhpStorm.
 * User: adachauer
 * Date: 28.10.17
 * Time: 15:39
 */

namespace webworks\CSVBundle\Lib;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ParseCSV
{

    private $file;
    private $delimiter;
    private $enclosure;
    private $headline;
    private $fileFirstLine;
    private $csvHeaderCells = [];
    private $csvData = [];

    /**
     * ParseCSV constructor.
     * @param $file
     * @param string $delimiter
     * @param string $enclosure
     * @param bool $headline
     */
    public function __construct($file, $delimiter = ';', $enclosure = '"', $headline = true)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException('File "' . $file . '" not found.');
        }

        $this->file = $file;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->headline = $headline;
    }

    /**
     * @param $content
     */
    private function parseHeader($content)
    {
        $this->csvHeaderCells = [];
        $cells = str_getcsv($content, $this->delimiter, $this->enclosure);
        foreach ($cells as $pos => $cellName) {
            $cellName = str_replace($this->enclosure, '', $cellName);
            if (substr($cellName, 0, 3) == b'\xef\xbb\xbf') {
                $cellName = substr($cellName, 3);
            }
            $this->csvHeaderCells[$pos] = trim($cellName);
        }
    }

    /**
     * @param $content
     * @param $count
     */
    private function parseLine($content, $count)
    {
        $cells = str_getcsv($content, $this->delimiter, $this->enclosure);
        #var_dump($cells);
        foreach ($cells as $pos => $cellVal) {
            $cellVal = str_replace($this->enclosure, '', $cellVal);
            if (isset($this->csvHeaderCells[$pos])) {
                $this->csvData[$count - 1][$this->csvHeaderCells[$pos]] = trim($cellVal);
            }
        }
    }

    /**
     * @return array
     */
    public function parse()
    {
        $fileReader = new FileReader();
        $fileReader->readLineByLine($this->file, [
            $this,
            'fileLineHandler'
        ]);

        return $this->csvData;
    }

    /**
     * @param array $args
     */
    public function fileLineHandler($args = [])
    {
        $content = $args['content'];
        $line = $args['count'];

        if ($line == 0) {
            $this->parseHeader($this->remove_utf8_bom($content));
        } else {
            $this->parseLine($this->remove_utf8_bom($content), $line);
        }
    }

    private function remove_utf8_bom($text)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
}
