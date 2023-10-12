# License headers

Command to launch on the module folder (after composer) for PHP files:  

```shell
php vendor/bin/header-stamp --license=202/license/license-php.txt --exclude=202,vendor,node_modules,tests,translations --extensions=php --display-report --header-discrimination-string="Younited"
```

For other files:
```shell
php vendor/bin/header-stamp --license=202/license/license.txt --exclude=202,vendor,node_modules,tests,translations --extensions=css,js,tpl --display-report --header-discrimination-string="Younited"
```
