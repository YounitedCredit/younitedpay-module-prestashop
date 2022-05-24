if [ ${TOTPSCLASSLIB_DEV_PATH} ] 
then 
    php ${TOTPSCLASSLIB_DEV_PATH}/classlib/refresh.php .
    find  ./vendor/totpsclasslib -name '*.php' -print0 | xargs -0  sed -ri -e 's!ounitedpay-prestashop!ounitedpay!g'
    find  ./vendor/composer -name '*.php' -print0 | xargs -0  sed -ri -e 's!ounitedpay-prestashop!ounitedpay!g'
    if [ -f ./controllers/admin/AdminYounitedpay-prestashopProcessLogger.php ]
    then
        rm ./controllers/admin/AdminYounitedpay-prestashopProcessLogger.php
    fi
fi
echo -e '\033[0;31m''delete vendor/symfony/deprecation-contracts/function.php for php56'
rm -f ./vendor/symfony/deprecation-contracts/function.php
echo -e '\033[0;31m''delete vendor/symfony/polyfill-php80/bootstrap.php for php56'
rm -f ./vendor/symfony/polyfill-php80/bootstrap.php
echo -e '\033[1;33m''copy both librairies corrected for php56'
cp -f ./202/compatibilityphp/symfony/deprecation-contracts/function.php ./vendor/symfony/deprecation-contracts/function.php
cp -f ./202/compatibilityphp/symfony/polyfill-php80/bootstrap.php ./vendor/symfony/polyfill-php80/bootstrap.php
if [ -f ./vendor/symfony/polyfill-72/php72.php ]
then
    echo -e '\033[0;31m''delete vendor/symfony/polyfill-72/php72.php for php56'
    rm -f ./vendor/symfony/polyfill-72/php72.php
    cp -f ./202/compatibilityphp/symfony/polyfill-php72/Php72.php ./vendor/symfony/polyfill-php72/Php72.php
fi