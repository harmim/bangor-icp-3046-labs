# Author: Dominik Harmim <harmim6@gmail.com>

PRODUCTION = 0
FIX = 0

CODE_CHECKER_DIR = vendor/nette/code-checker
CODE_CHECKER = ./$(CODE_CHECKER_DIR)/code-checker


.PHONY: install
install: composer npm grunt


.PHONY: composer
composer:
ifeq ($(PRODUCTION), 1)
	composer install --no-dev --no-interaction
else
	composer install --no-interaction
endif


.PHONY: npm
npm:
ifeq ($(PRODUCTION), 1)
	npm install --production
else
	npm install
endif


.PHONY: grunt
grunt:
	node_modules/.bin/grunt default


.PHONY: code-checker
code-checker: composer
	cd $(CODE_CHECKER_DIR) && composer install
ifeq ($(FIX), 1)
	$(CODE_CHECKER) -d src/ --short-arrays --strict-types -l -f
	$(CODE_CHECKER) -d www/ -i js/ -i css/ --short-arrays --strict-types -l -f
else
	$(CODE_CHECKER) -d src/ --short-arrays --strict-types -l
	$(CODE_CHECKER) -d www/ -i js/ -i css/ --short-arrays --strict-types -l
endif


.PHONY: coding-standard
coding-standard: composer
ifeq ($(FIX), 1)
	vendor/bin/ecs check src/classes src/functions --config vendor/nette/coding-standard/coding-standard-php71.neon --fix
else
	vendor/bin/ecs check src/classes src/functions --config vendor/nette/coding-standard/coding-standard-php71.neon
endif
