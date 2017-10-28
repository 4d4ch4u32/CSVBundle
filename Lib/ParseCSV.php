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
        if (strlen($content) > 0 && $this->headline == true) {
            $cells = explode($this->delimiter, $content);
            foreach ($cells as $pos => $cellName) {
                $cellName = str_replace($this->enclosure, '', $cellName);
                $this->csvHeaderCells[$pos] = $cellName;
            }
        }
    }

    /**
     * @param $content
     * @param $count
     */
    private function parseLine($content, $count)
    {
        if (strlen($content) > 0 && $this->headline == true) {
            $cells = explode($this->delimiter, $content);
            foreach ($cells as $pos => $cellVal) {
                $cellVal = str_replace($this->enclosure, '', $cellVal);
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
            $this->parseHeader($content);
        } else {
            $this->parseLine($content, $line);
        }
    }
}
