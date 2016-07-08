# <a name="title">DOMArch app [BETA]</a>

The DOMArch app context, sessions, pages & forms, encrypted urls, no business logic... just a service interface

## <a name="summary">Summary</a>
* [Installation](#installation)
* [Components](#components)
* [License](#license)

## <a name="installation">Installation :</a>

<strong>If you change the following example names, please adapt your `config.json`</strong>

* Firstly, install [DOMArch](https://github.com/dom-arch/dom-arch)
* Secondly, install the [DOMArch service context](https://github.com/dom-arch/service)
* Clone this repository into your `entrypoints` directory
   `git clone https://github.com/dom-arch/app.git app`
* Add a host, like `domain.tld`, to your `hosts` file
* Create a database like `domain-tld-app`
* Go to the `sql` directory and execute each table script
* In a shell, go to your `app` directory and exectute the following commands :
  * `composer install -o`
  * `php cli/setup.php`
* Go to http://app.domain.tld

## <a name="components">Components :</a>

* [Assemblies](./doc/assemblies.md)
* [Entities and Repositories](./doc/entities-and-repositories.md)
* [Lib](./doc/lib.md)
* [Modules](./doc/modules.md)
* [Providers](./doc/providers.md)

## <a name="license">License :</a>
This project is MIT licensed.

Copyright © 2015 - 2016 Lcf.vs
