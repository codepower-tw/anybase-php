.PHONY: all help install test coverage cs check stan clean

all: check stan test ## Run all CI checks locally (style + static analysis + tests)

help: ## List available targets
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_-]+:.*## / {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install composer dependencies
	composer install --prefer-dist --no-progress

test: ## Run the test suite
	vendor/bin/pest

coverage: ## Run the test suite with coverage report
	XDEBUG_MODE=coverage vendor/bin/pest --coverage

cs: ## Apply PSR-12 fixes
	vendor/bin/php-cs-fixer fix

check: ## Verify PSR-12 compliance (no fixes)
	vendor/bin/php-cs-fixer fix --dry-run --diff

stan: ## Run PHPStan static analysis on src/
	vendor/bin/phpstan analyse

clean: ## Remove generated caches and artifacts
	rm -rf vendor .phpunit.cache .php-cs-fixer.cache coverage.txt
