./vendor/bin/phpunit -c phpunit.xml.dist --coverage-clover clover.xml
composer require php-coveralls/php-coveralls --dev --prefer-source
echo "json_path: coveralls-upload.json" >> .coveralls.yml
echo "coverage_clover: clover.xml" >> .coveralls.yml
./vendor/bin/php-coveralls -c .coveralls.yml
