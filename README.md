# Laravel SAP Driver 

> SAP features integrated with Laravel.

![Maintainer](https://img.shields.io/badge/maintainer-Eliel%20Ferreira-informational)
![PHP](https://img.shields.io/badge/PHP->=7.3-blueviolet)
![VERSION](https://img.shields.io/badge/stable-v1.0.0-blue)
![BUILD](https://img.shields.io/badge/build-pass-success)
![LICENSE](https://img.shields.io/badge/license-MIT-success)

## Installation

Make sure you have the [php7-sapnwrfc](https://gkralik.github.io/php7-sapnwrfc/index.html) extension installed.

``` composer require elielelie/laravel-sap ```

To establish the connection with SAP, you need to add the following variables to your `.env` file:

```
SAP_HOST        Host
SAP_SYSTEM      System number
SAP_LANGUAGE    Language default
SAP_CLIENT      Client instance
SAP_USERNAME    Username
SAP_PASSWORD    Password    
```

You can publish the configuration file and add new connections by running:

```$ php artisan vendor:publish --provider="Elielelie\Sap\SapServiceProvider" ```

## Usage

#### Connecting to SAP

```php
<?php

use Elielelie\Sap\Sap;

$sap = app(Sap::class);

$connection = $sap->open();

or

// Connection name defined in the configuration file config/sap.php
 
$connection = $sap->open('name');

```

#### Perform Function Module call

```php
<?php

// ... connection

$function = $connection->fm('BAPI_USER_GET_DETAIL');

// Get function description.
print_r($function->description());

// Add import parameter.
$function->param('USERNAME', 'USER');

// Perform function call and retrieve result.
$results = $function->execute();

$connection->close();

```

Getting details about a user using `RFC_READ_TABLE`

```php
<?php

// ... connection

$function = $connection->fm('RFC_READ_TABLE');

$function->param('QUERY_TABLE', 'USR01')
	->param('OPTIONS', [
		['TEXT' => 'BNAME = 'USER' OR BNAME = 'USER2' OR BNAME LIKE 'USER5*']
	])
	->param('ROWCOUNT', 5)
	->param('DELIMITER', '~')
;

$result = $function->execute();

$connection->close();

```

#### Query Builder usage

```php
<?php

// ... connection

$fm = $connection->fmc(Table::class);

$results = $fm->table('USR01')
    ->fields(['BNAME', 'STCOD', 'MANDT' ...])
	->where('bname', ['USER', 'USER5'])
	->orWhere('bname', 'LIKE', 'USER5*')
	->limit(5)
	->get()
;

```


## License

This software is licensed under the MIT license. See [LICENSE](LICENSE) for details.

## Legal Notice

SAP and other SAP products and services mentioned herein are trademarks or registered trademarks of SAP SE (or an SAP affiliate company) in Germany and other countries.
