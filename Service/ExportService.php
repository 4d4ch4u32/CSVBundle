<?php
/**
 * Created by PhpStorm.
 * User: adachauer
 * Date: 27.10.17
 * Time: 18:58
 */

namespace webworks\CSVBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use webworks\CSVBundle\Lib\createCSV;

class ExportService
{

    private $container;
    private $mapping = [];
    private $mappingName;
    private $csvPath = "";
    private $csvData = "";
    private $data = [];
    private $mode;

    /**
     * MappingService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mapping = $this->container->getParameter('webworks_csv_mapping');
    }

    /**
     * @return array
     */
    public function getMappingArray()
    {
        return $this->mapping;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setMappingName($name)
    {
        $this->mappingName = $name;

        return $this;
    }

    /**
     * @param $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function process()
    {
        if (!isset($this->mapping[$this->mode][$this->mappingName])) {
            throw new \Exception('Mapping for "' . $this->mappingName . '" not found.');
        }

        $entity = $this->mapping[$this->mode][$this->mappingName]['class'];
        if (!class_exists($entity)) {
            throw new \Exception('Class "' . $entity . '" not found.');
        }

        $mapping = $this->mapping[$this->mode][$this->mappingName]['mapping'];
        $mappedData = [];
        if (sizeof($mapping) > 0) {
            $rowCount = 0;

            $em = $this->container->get('doctrine.orm.default_entity_manager');
            $objectsFromDB = $em->getRepository($entity)
                ->findAll();

            if (sizeof($objectsFromDB) > 0) {
                foreach ($objectsFromDB as $object) {
                    foreach ($mapping as $csvColName => $entityField) {
                        $mappedData[$rowCount][$csvColName] = $this->getEntityValue($object, $entityField);
                    }
                    $rowCount++;
                }
            }
        }

        $this->data = $mappedData;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (!file_exists($this->csvPath)) {
            $tmpDir = sys_get_temp_dir();
            $now = new \DateTime();
            $filePath = $tmpDir . DIRECTORY_SEPARATOR . $this->mappingName . '_' . $now->format('d-m-y-H-i') . '_' . rand(0, 999) . '.csv';

            file_put_contents($filePath, $this->getCSV());

            $this->csvPath = $filePath;
        }

        return $this->csvPath;
    }

    /**
     * @return string
     */
    public function getCSV()
    {
        if (sizeof($this->getData()) > 0) {
            $ccsv = new createCSV();
            $this->csvData = $ccsv->arrayToCsv($this->getData());
        }

        return $this->csvData;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $entity
     * @param $field
     * @return string
     */
    private function getEntityValue($entity, $field)
    {
        $value = '';

        $fields = explode(',', $field);

        foreach ($fields as $fieldsItem) {
            $getter = 'get' . trim($fieldsItem);

            if (strlen($value) == 0) {
                $value = $entity->$getter();
            } else {
                $value .= " " . $entity->$getter();
            }
        }

        return trim($value);
    }
}
