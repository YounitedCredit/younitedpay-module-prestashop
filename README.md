# YounitedPay PrestaShop Addons

[![Coding Standart](https://github.com/YounitedCredit/younitedpay-module-prestashop/actions/workflows/php.yml/badge.svg)](https://github.com/YounitedCredit/younitedpay-module-prestashop/actions/workflows/php.yml) [![Unit test](https://github.com/YounitedCredit/younitedpay-module-prestashop/actions/workflows/phpunit.yml/badge.svg)](https://github.com/YounitedCredit/younitedpay-module-prestashop/actions/workflows/phpunit.yml)

## About

Younited Credit payment solution for PrestaShop.  
&nbsp;  

### Product page on PrestaShop Addons  

This addons is not avalable on PrestaShop Addons.
This Git repository is for developpers only.
Please contact YounitedPay customer service to get  
&nbsp;  

## Requirements

PHP version (check Module version guide)  
&nbsp;  

## Installation for merchands

To install module on PrestaShop, download zip package supply by YounitedPay customer service or the zip file
named [v1.x.x-prod-younitedpay.zip](https://github.com/YounitedCredit/younitedpay-module-prestashop) attached on each detail release page.  
&nbsp;  

## Installation for developpers

If you are a developper, this module contain composer.json.dist file. If you clone or download the module from github
repository, run the ```composer install``` is not necessary. You can see why on [module documentation][module-doc] on "Guzzle trouble".

See the [composer documentation][composer-doc] to learn more about the composer.json file.  
&nbsp;  

## Contributing

PrestaShop modules are open-source extensions to the PrestaShop e-commerce solution. Everyone is welcome and even encouraged to contribute with their own improvements.  
&nbsp;  

## Requirements to make a contribution

Contributors **must** follow the following rules:

* **Make your Pull Request on the "develop" branch**, NOT the "master" branch.
* Do not update the module's version number.
* Follow [the coding standards][1].  
&nbsp;  

## Process in details

Contributors wishing to edit a module's files should follow the following process:

1. Create your GitHub account, if you do not have one already.
2. Fork the YounitedCredit/younitedpay-module-prestashop project to your GitHub account.
3. Clone your fork to your local machine in the ```/modules/younitedpay``` directory of your PrestaShop installation.
4. Create a branch in your local clone of the module for your changes.
5. Change the files in your branch. Be sure to follow [the coding standards][1]!
6. Push your changed branch to your fork in your GitHub account.
7. Create a pull request for your changes **on the _'develop'_ branch** of the module's project. Be sure to follow [the commit message norm][2] in your pull request. If you need help to make a pull request, read the [Github help page about creating pull requests][3].
8. Wait for one of the core developers either to include your change in the codebase, or to comment on possible improvements you should make to your code.

That's it: you have contributed to this open-source project! Congratulations!  
&nbsp;  

## Command line launched by github actions

Please launch these command line before submitting a Pull Request.

The name of the git folder must be "younitedpay" in order to work correctly with these docker commands.  

### phpcs fixer

```bash
~modules/younitedpay$ vendor/bin/php-cs-fixer --fix
```

#### phpstan

You need a docker container to launch phpstan

```bash
# create the prestashop container
~modules/younitedpay$ docker run -tid --rm -v ps-volume:/var/www/html --name temp-ps prestashop/prestashop

# launch phpstan
~modules/younitedpay$ docker run --rm --volumes-from temp-ps -v $PWD:/var/www/html/modules/younitedpay -e _PS_ROOT_DIR_=/var/www/html --workdir=/var/www/html/modules/younitedpay phpstan/phpstan:0.12 analyse --configuration=/var/www/html/modules/younitedpay/202/phpstan/phpstan.neon
```

### phpunit

You need a docker container to launch phpunit

```bash
docker run -tid --rm -v $PWD:/var/www/html/modules/younitedpay --name temp-unittest-ps 202ecommerce/prestashop:1.7.8.3
docker exec -t temp-unittest-ps sh /var/www/html/modules/younitedpay/202/docker/run_for_unittest.sh
```

[1]: https://devdocs.prestashop.com/1.7/development/coding-standards/
[2]: http://doc.prestashop.com/display/PS16/How+to+write+a+commit+message
[3]: https://help.github.com/articles/using-pull-requests
[composer-doc]: https://getcomposer.org/doc/04-schema.md
[module-doc]: https://docs.202-ecommerce.com/younitedpay/