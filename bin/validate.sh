#!/usr/bin/env bash

    tools/php-cs-fixer fix --allow-risky yes --dry-run -v &&
    tools/phpstan analyse src -c phpstan.neon --level 2 &&
    tools/phan --progress-bar --minimum-severity 2
    vendor/bin/phpunit --verbose --coverage-clover build/logs/clover.xml
