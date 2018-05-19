[![Build Status](https://img.shields.io/travis/patrickkerrigan/php-xray.svg?style=flat-square)](https://travis-ci.org/patrickkerrigan/php-xray) [![Maintainability](https://api.codeclimate.com/v1/badges/548ad6b7c25bef8004cd/maintainability)](https://codeclimate.com/github/patrickkerrigan/php-xray/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/548ad6b7c25bef8004cd/test_coverage)](https://codeclimate.com/github/patrickkerrigan/php-xray/test_coverage) [![PHP 7.0](https://img.shields.io/badge/php-7.0-blue.svg?style=flat-square)](http://php.net/)  [![Packagist](https://img.shields.io/packagist/v/pkerrigan/xray.svg?style=flat-square)](https://packagist.org/packages/pkerrigan/xray)

# pkerrigan\xray
A basic PHP instrumentation library for AWS X-Ray

Until Amazon releases an official PHP SDK for AWS X-Ray this library allows you to add basic instrumentation to PHP applications and report traces via the AWS X-Ray daemon.

Please note that no automatic instrumentation of popular libraries is provided. In order to instrument SQL queries, HTTP requests and/or other services you'll be required to create your own wrappers which start and end tracing segments as appropriate.

## Installation

The recommended way to install this library is using Composer:

```bash
$ composer require pkerrigan/xray ^1.0
```

## Usage

### Starting a trace

The ```Trace``` class represents the top-level of an AWS X-Ray trace, and can function as a singleton for easy access from anywhere in your code, including before frameworks and dependency injectors have been initialised.

You should start a trace as early as possible in your request:

```php
use Pkerrigan\Xray\Trace;

Trace::getInstance()
    ->setTraceHeader($_SERVER['HTTP_X_AMZN_TRACE_ID'] ?? null)
    ->setName('app.example.com')
    ->setUrl($_SERVER['REQUEST_URI'])
    ->setMethod($_SERVER['REQUEST_METHOD'])
    ->begin(); 
```

### Adding a segment to a trace

You can add as many segments to your trace as necessary, including nested segments. To add an SQL query to your trace, you'd do the following:

```php
Trace::getInstance()
    ->getCurrentSegment()
    ->addSubsegment(
        (new SqlSegment())
            ->setName('db.example.com')
            ->setDatabaseType('PostgreSQL')
            ->setQuery($mySanitisedQuery)    // Make sure to remove sensitive data before passing in a query
            ->begin()    
    );
    
    
// Run your query here
    
Trace::getInstance()
    ->getCurrentSegment()
    ->end();
    
```

The ```getCurrentSegment()``` method will always return the most recently opened segment, allowing you to nest as deeply as necessary.

### Ending a trace

At the end of your request, you'll want to end and submit your trace. By default only submitting via the AWS X-Ray daemon is supported.

```php
Trace::getInstance()
    ->end()
    ->setResponseCode(http_response_code())
    ->submit(new DaemonSegmentSubmitter());
```

## Features not yet implemented

* Sending user info such as IP address and User Agent with a trace
* Exception and stack trace support
* Submission of incomplete segments
