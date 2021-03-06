# Freeradius Web

[![Circle CI](https://circleci.com/gh/esroyo/freeradius-web.svg?style=svg)](https://circleci.com/gh/esroyo/freeradius-web)

An  API to [Freeradius](http://freeradius.org) stats. Build on [Laravel](https://laravel.com).
_Note: very early stage of development._

## Features
* Access radius account session activity data by session, day or total.

## Install
Clone and install dependencies:
```
git clone https://github.com/esroyo/freeradius-web.git
cd freeradius-web
composer install
```
Copy .env file and fill the **freeradius mysql database** information:
```
cp .env.example .env
vim .env
```
Create/populate the database:
```
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

## Give a try
Spin a test server:
```
cd public
php -S localhost:8080
```

Login and obtain a Json Web Token:
```
curl -X POST -H "Content-type: application/json" --data '{"email":"admin@localhost","password":"admin"}' http://localhost:8080/api/login
```

Copy the response `token` and replace `_JWT_TOKEN_` in the following command example:
```
curl -H "Authorization: Bearer _JWT_TOKEN_" "http://localhost:8080/api/v1/radacct?start_date=20160520&end_date=20160521&timezone=Europe/Berlin&granularity=day&metrics=sessiontime,inputoctets,outputoctets&dimension=username"
```
You'll get something, provided that you have data on your `radacct` table for the requested dates ;)
```json
{
    "user1": [
        {
            "starttime": "2016-05-20 00:00:00",
            "stoptime": "2016-05-20 23:59:59",
            "sessiontime": 49962,
            "inputoctets": 26339924,
            "outputoctets": 11710366
        },
        {
            "starttime": "2016-05-21 00:00:00",
            "stoptime": "2016-05-21 23:59:59",
            "sessiontime": 1872,
            "inputoctets": 4323,
            "outputoctets": 6
        }
    ],
    "user2": [
        {
            "starttime": "2016-05-20 00:00:00",
            "stoptime": "2016-05-20 23:59:59",
            "sessiontime": 6116,
            "inputoctets": 17304613,
            "outputoctets": 495135809
        }
    ]
}
```

## API

### Authorization
Requires to past a valid Json Web Token in the headers of your HTTP request:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBpL2xvZ2luIiwiaWF0IjoxNDY2NDM3Mjk1LCJleHAiOjE0NjY0NDA4OTUsIm5iZiI6MTQ2NjQzNzI5NSwianRpIjoiYzE1MmYwZWQ3ZGM5YzMzMTQ1Yzk3YzM1NTk0MTY3ODciLCJzdWIiOjV9.v4bVMnR4Op7rnOgAhcYedjN-TUUz6kw92ll-ysbq8Yc
```

### Radacct parameters

#### start_date

```
start_date=20160520
```

*Required.*

Any string accepted by PHP's [DateTime](http://php.net/manual/en/datetime.formats.php).

#### end_date

```
end_date=20160530
```

*Required.*

Any string accepted by PHP's [DateTime](http://php.net/manual/en/datetime.formats.php).

#### timezone

```
timezone=Europe/Berlin
```

*Required.*

Any of the PHP's [timezone identifiers list](http://php.net/manual/en/datetimezone.listidentifiers.php).

#### granularity

```
granularity=day
```

*Required.*

One of:
* `session`: get each radacct session individually.
* `day`: aggregate sessions by day.
* `total`: sum of all the sessions.

#### metrics

```
metrics=sessiontime,inputoctets,outputoctets
```

*Required.*

Comma sepparated list of:
* `sessiontime`
* `inputoctets`
* `outputoctets`

#### dimension

```
dimension=username
```

One of:
* `username`
* `groupname`
* `realm`
* `nasipaddress`
* `nasportid`
* `nasportype`
* `calledstationid`
* `callingstationid`
* `servicetype`
* `framedprotocol`
* `framedipaddres`

#### filters

```
filters=username%3D%3Dadmin
```

The `filters` parameter restricts the data returned from your request. To use the filters parameter, supply a dimension or metric on which to filter, followed by the filter expression.

It is a comma sepparated list of single filter expressions.

A single filter expression uses the form:
```name operator value```

Where:
* _name_ — the name of the dimension or metric to filter on.
* _operator_ — defines the type of filter match to use. Operators are specific to either dimensions or metrics.
* _value_ — states the values to be included in or excluded from the results.

##### Filter Operators

The operators must be URL-encoded in order to be included in URL query strings.

* `%3D%3D` (`==`) Equals
* `!%3D` (`!=`) Does not equal
