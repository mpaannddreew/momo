## Momo

Momo is a simple library in PHP for the MTN Mobile Money Open API.

## Product support

* Collections
* Disbursements
* Remittances

## Getting Started

* Signup For An Account
* Subscribe To Products

## Signup For An Account

Follow this link to our developer portal and [signup](https://momodeveloper.mtn.com/signup.html)
for an account.

## Subscribe To Products

On the [Products](https://momodeveloper.mtn.com/products.html) page on developer portal you should see items you can subscribe to:

* Collections
* Disbursements
* Remittances

## Installation

The recommended way to install Momo is through [composer](http://getcomposer.org).

Just create a composer.json file for your project and require it:

```TERMINAL
composer require fannypack/momo
```
Now you can add the autoloader, and you will have access to the library:

```php
<?php
require 'vendor/autoload.php';
```

## Usage
### Creating a product instance

```php
<?php
use FannyPack\Momo\Products\Collection;
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

$options = [
    // 'callbackHost' => '', //(optional) default is http://localhost:8000
    // 'callbackUrl' => '', //(optional) default is http://localhost:8000/callback
    // 'environment' => '', //(optional) default is sandbox
    // 'accountHolderIdType' => '', //(optional) default is msisdn
    'subscriptionKey' => '', //Product Subscription key
    'xReferenceId' => '', //Api user reference id
    'apiKey' => '', // Api user key
    //'preApproval' => '', //(optional) default is false
    //'accessToken' => '' //Required for transactions
];

// Using collection
$collection = Collection::create($options);

// Using disbursement
$disbursement = Disbursement::create($options);

// Using remittance
$remittance = Remittance::create($options);

```

### Sandbox User Provisioning
#### Create API User
```php
<?php
use FannyPack\Momo\Products\Collection;
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using collection
$product = Collection::create($options);

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$product->createApiUser(); //{"statusCode": 201}

```

#### GET API User Details
```php
<?php
use FannyPack\Momo\Products\Collection;
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using collection
$product = Collection::create($options);

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$apiUser = $product->getApiUser();
$apiUser->getProviderCallbackHost(); //http://localhost:8000
$apiUser->getTargetEnvironment(); //sandbox

```

#### Create API Key
```php
<?php
use FannyPack\Momo\Products\Collection;
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using collection
$product = Collection::create($options);

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$apiKey = $product->createApiKey();
$apiKey->getApiKey(); //apiKey

```

### Oauth 2.0
#### Get token
```php
<?php
use FannyPack\Momo\Products\Collection;
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using collection
$product = Collection::create($options);

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$token = $product->getToken();
$token->getAccessToken(); //accessToken
$token->getTokenType(); //tokenType
$token->getExpiresIn(); //expiry in seconds

```

### Transactions
#### Get account balance
```php
<?php
use FannyPack\Momo\Products\Collection;
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using collection
$product = Collection::create($options);

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$balance = $product->getAccountBalance();
$balance->getAvailableBalance(); //accountBalance
$balance->getCurrency(); //currency

```
#### Get account holder status
```php
<?php
use FannyPack\Momo\Products\Collection;
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using collection
$product = Collection::create($options);

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$product->getAccountHolderInfo($accountHolderId); //{"statusCode": 201}

```

### Collections
#### Request to pay
```php
<?php
use FannyPack\Momo\Products\Collection;

// Using collection
$product = Collection::create($options);

$product->requestToPay($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = ''); // {"statusCode": 200, "financialTransactionId": "8f3077a6-ce43-4584-a425-589c50cfcbe4"}

```
#### Request to pay status
```php
<?php
use FannyPack\Momo\Products\Collection;

// Using collection
$product = Collection::create($options);

$transactionStatus = $product->getRequestToPayStatus($financialTransactionId);
$transactionStatus->getAmount(); //amount
$transactionStatus->getCurrency(); //currency
$transactionStatus->getExternalId(); //externalId
$transactionStatus->getPayer(); //payer object
$transactionStatus->getStatus(); //status

```
### Disbursements and Remittances
#### Transfer
```php
<?php
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$product->transfer($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = '');// {"statusCode": 200, "financialTransactionId": "8f3077a6-ce43-4584-a425-589c50cfcbe4"}

```
#### Transfer status
```php
<?php
use FannyPack\Momo\Products\Disbursement;
use FannyPack\Momo\Products\Remittance;

// Using disbursement
$product = Disbursement::create($options);

// Using remittance
$product = Remittance::create($options);

$transactionStatus = $product->transferStatus($financialTransactionId);
$transactionStatus->getAmount(); //amount
$transactionStatus->getCurrency(); //currency
$transactionStatus->getExternalId(); //externalId
$transactionStatus->getPayer(); //payer object
$transactionStatus->getStatus(); //status

```

### Bugs
For any bugs found, please email me at andrewmvp007@gmail.com or register an issue at [issues](https://github.com/mpaannddreew/momo/issues)

