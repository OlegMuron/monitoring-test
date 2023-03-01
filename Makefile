APP_NAME = Monitoring-test

SHELL ?= /bin/bash
ARGS = $(filter-out $@,$(MAKECMDGOALS))

IMAGE_TAG = latest
IMAGE_NAME = monitoring-test/app

APP_PATH = $(pwd)

BUILD_ID ?= $(shell /bin/date "+%Y%m%d-%H%M%S")

.SILENT: ;               # no need for @
.ONESHELL: ;             # recipes execute in same shell
.NOTPARALLEL: ;          # wait for this target to finish
.EXPORT_ALL_VARIABLES: ; # send all vars to shell
Makefile: ;              # skip prerequisite discovery

# Run make help by default
.DEFAULT_GOAL = help

ifneq ("$(wildcard ./VERSION)","")
VERSION ?= $(shell cat ./VERSION | head -n 1)
else
VERSION ?= 0.0.1
endif

.PHONY: .env
.env: ## Make .env file
	 cp $@.example $@

# Public targets
.PHONY: .title
.title:
	printf "\n                \033[95m%s: v%s\033[0m\n" "$(APP_NAME)" $(VERSION)


.PHONY: build
build: ## Build or rebuild service image
	docker build \
		--build-arg VERSION=$(VERSION) \
		--build-arg BUILD_ID=$(BUILD_ID) \
		-t $(IMAGE_NAME):$(IMAGE_TAG) \
		--no-cache \
		--force-rm .

.PHONY: up
up: network ## Starts and attaches to containers for a service
ifeq ("$(wildcard ./docker-compose.env)","")
	cp ./docker-compose.env.dist ./docker-compose.env
endif
	docker-compose up -d

.PHONY: install
install: up ## Install composer dependencies
	docker-compose exec app composer install

.PHONY: ps
ps: ## List started containers
	docker-compose ps

.PHONY: sh
sh: up ## Go to the application container (if any)
	docker-compose exec app sh

.PHONY: prune
prune: ## Stop, kill and purge project containers
	docker-compose down
	docker volume prune -f
	docker system prune -f

.PHONY: network
network: ## Create network
	docker network create monitoring-test 2> /dev/null | true

.PHONY: stop
stop: ## Stop containers
	docker-compose stop
	docker-compose ps

.PHONY: restart
restart: ## Restart containers
	docker-compose restart
	docker-compose ps

.PHONY: stats
stats: ## Show containers info (CPU, Mem, PIDs, Status, Ports etc.)
	docker stats \
		--no-stream \
		--format \
			"table {{.Name}}\t{{.CPUPerc}}\t{{.MemPerc}}\t{{.MemUsage}}\t{{.NetIO}}\t{{.BlockIO}}\t{{.PIDs}}"

.PHONY: load-without-request-to-dbs
load-stub: ## Make 1000 req per 5 thread to the stub page
	 ab -n 1000 -c 5 http://127.0.0.1:81/

.PHONY: load-test
load-test: ## Make 1000 req per 5 thread to the test page with queries to MongoDB and Elastic
	 ab -n 1000 -c 5 http://127.0.0.1:81/test

.PHONY: build-siege
build-siege: ## Build the Siege image
	docker build ./docker/siege -t monitoring-test/siege:$(IMAGE_TAG)

.PHONY: siege
siege: ## Siege it!!! ('make siege --help' for getting help or 'make siege -r20 -c1000 --no-follow http://app/test' for siege this application)
	docker-compose run -t siege -R /siege/.siegerc ${ARGS}

.PHONY: help
help: .title ## Show this help and exit (default target)
	echo ''
	printf "                %s: \033[94m%s\033[0m \033[90m[%s] [%s]\033[0m\n" "Usage" "make" "target" "ENV_VARIABLE=ENV_VALUE ..."
	echo ''
	echo '                Available targets:'
	# Print all commands, which have '##' comments right of it's name.
	# Commands gives from all Makefiles included in project.
	# Sorted in alphabetical order.
	echo '                ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	grep -hE '^[a-zA-Z. 0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		 awk 'BEGIN {FS = ":.*?## " }; {printf "\033[36m%+15s\033[0m: %s\n", $$1, $$2}'
	echo '                ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	echo ''

%:
	@:
