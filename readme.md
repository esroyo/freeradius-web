# Freeradius Web

API to your [Freeradius](http://freeradius.org) stats.

## Install
Clone and install dependencies:
```
git clone https://github.com/esroyo/freeradius-web.git
cd freeradius-web
composer install
```
Copy .env file and fill the freeradius mysql database information:
```
cp .env.example .env
vim .env
```
Create/populate the database:
```
php artisan migrate --seed
```

## Give a try
Spin a test server:
```
cd public
php -S localhost:8080
```

Copy an `api_token` from the `users` table (`SELECT api_token FROM users LIMIT 1`).
Then replace `_YOUR_API_TOKEN_` in the following command example:
```
curl -X POST -d 'start_date=20160520' -d 'end_date=20160530' -d 'timezone=UTC' -d 'granularity=day' -d 'metrics=sessiontime,inputoctets,outputoctets' -d 'dimension=username' -d 'api_token=_YOUR_API_TOKEN_' http://localhost:8080/api/v1/reports/radacct
```
You'll get some JSON, provided that you have some data on your `radacct` table for the requested dates ;)

## API

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
