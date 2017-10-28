webworksCSVBundle
=============
The `webworksCSVBundle` helps you to handle csv data and files in your Symfony application. It provides a simple YAML based mapping for doctrine.

## Requirements

* PHP 7.x
* Symfony 3.x
* See also the composer.json file

## Installation

### Step 1: Download webworksCSVBundle using composer

Require the bundle with composer:

```
composer require webworksnbg/csv-bundle "~0.1"
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
// app/AppKernel.php
 
public function registerBundles()
{
    $bundles = array(
        // ...
        new webworks\CSVBundle\webworksCSVBundle(),
        // ...
    );
}
```

### Step 3: Create the mapping YAML file

Create a YAML file with the following content (for example):

``` yaml
# app/config/csv/mapping.yml
 
import:
# not supported in the current version
 
export:
    clients: # mapping name 
        class: acme\AppBundle\Entity\Kunde
        mapping:
            name:         firstname, lastname
            street:       street
            city:         zip, city
            email:        email
            company:      company_name
```

**Note**: Currently, only one export of the data is possible. The import function will follow shortly

### Step 4: Use the mapping service to write data from your database to csv

You can call the service from container:
 
``` php
// where ever you have an container
 
$csvPath = $this->getContainer()->get('webworks.csv.mapping')
    ->setMappingName('clients') // mapping name
    ->setMode('export')
    ->process()
    ->getPath(); // Temporary file path!
```

### Step 5: Enjoy :)

Now, you can easily write data from your database via doctrine to csv.

## Credits

This bundle has been created by [webworks nürnberg](http://webworks-nuernberg.de) and the community.

## Examples

### Parse CSV file
``` php
use webworks\CSVBundle\Lib\ParseCSV;
 
$parser = new ParseCSV('/var/www/html/saleshare/app/config/dl.csv', ';', '"');
$data = $parser->parse();
```
