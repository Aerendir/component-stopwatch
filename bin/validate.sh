#!/usr/bin/env bash

    tools/php-cs-fixer fix --allow-risky yes --dry-run -v &&
    tools/phpstan analyse src -c phpstan.neon --level max &&
    tools/phan --progress-bar --minimum-severity max
    vendor/bin/phpunit
