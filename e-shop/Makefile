# Author: Dominik Harmim <harmim6@gmail.com>

PRODUCTION = 0


.PHONY: install
install: npm grunt


.PHONY: npm
npm:
ifeq ($(PRODUCTION), 1)
	npm install --production
else
	npm install
endif


.PHONY: grunt
grunt:
	node_modules/grunt-cli/bin/grunt default
