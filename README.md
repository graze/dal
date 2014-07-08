# DAL (Data Access Layer)

<img src="diagram.png" alt="Graze.com" align="right" width=240/>

**Master build:** `Unknown`<br/>
**Requires:** `PHP >= 5.4 || HHVM >= 3.0`

**DAL** is a data access layer for PHP, built to add an additional layer of
abstraction between your application and persistence layers. The main goal of
this library is to allow use of multiple persistence layers, each potentially
employing different abstraction patterns, under a *single* interface.

The [data mapper pattern][data-mapper] is great for keeping the particulars of
data persistence hidden behind its interfaces. DAL improves upon this
abstraction to include multiple persistence layers while providing underlying
support for patterns other than data mapper (e.g.
[active record][active-record]).

We're using this in-house to move our application towards a more manageable data
mapper layer rather than our current active record implementation. This will be
our interface into persistence across our PHP applications for the foreseeable
future. Wel will continue to use our active record implementation underneith DAL
until we decide to remove it completely, at which point DAL will stay put.

It can be installed in whichever way you prefer, but we recommend
[Composer][packagist].
```json
{
    "repositories": [
        {
            "type": "git",
            "url": "git@github.com:graze/dal.git"
        }
    ],
    "require": {
        "graze/dal": "*"
    }
}
```

## Documentation
This documentation will need *a lot* of work done to make it complete. For now,
though, this really simple example will have to do.
```php
<?php

$dm = new DalManager([new MyAdapter(/*config*/) /*[,...]*/]);

$repo = $dm->getRepository('Profile');
$profile = $repo->findByEmail('andrew.lawson@graze.com');

$profile->setEmail('jake@graze.com');

$dm->flush();
```

## Contributing
Contributions are accepted via Pull Request, but passing unit tests must be
included before it will be considered for merge.
```bash
$ curl -O https://raw.githubusercontent.com/adlawson/vagrantfiles/master/php/Vagrantfile
$ vagrant up
$ vagrant ssh
...

$ cd /srv
$ composer install
$ vendor/bin/phpunit
```

### License
The content of this library is released under the **MIT License** by
**Nature Delivered Ltd**.<br/> You can find a copy of this license in
[`LICENSE`][license] or at http://opensource.org/licenses/mit.

<!-- Project links -->
[travis]: https://travis-ci.org/graze/dal
[travis-master]: https://travis-ci.org/graze/dal.png?branch=master
[packagist]: https://packagist.org/packages/graze/dal

<!-- References -->
[data-mapper]: http://en.wikipedia.org/wiki/Data_mapper_pattern
[active-record]: http://en.wikipedia.org/wiki/Active_record_pattern

<!-- Files -->
[license]: /LICENSE
