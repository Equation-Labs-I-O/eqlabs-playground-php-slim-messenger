SHELL				:= /bin/bash
UID 				:= $(shell id -u)
GID 				:= $(shell id -g)
DOCKER_COMPOSE		:= docker compose -f docker-compose.yaml

help:
	@echo "${GREEN}-------------- USAGE  --------------------------------------${RESET}"
	@echo "$ make ${GREEN}target${RESET} [options] "
	@echo "${GREEN}-------------- Available Targets ---------------------------${RESET}"
	@grep -E '^[a-zA-Z_0-9%-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "${GREEN}%-20s${RESET} %s\n", $$1, $$2}'

build: ## Build the images
	@$(DOCKER_COMPOSE) down --remove-orphans -v --rmi local || $(MAKE) .cleanup
	@$(DOCKER_COMPOSE) build --pull || $(MAKE) .cleanup
start: ## Start the stack
	@$(DOCKER_COMPOSE) up -d || $(MAKE) .cleanup
stop: ## Stop the stack
	@$(DOCKER_COMPOSE) down --remove-orphans -v
run:
	@$(DOCKER_COMPOSE) run --rm app sh -c "$(CMD)"
follow-logs: ## Show the logs
	@$(DOCKER_COMPOSE) logs app --follow


## Helpers Functions
.cleanup:
	@$(DOCKER_COMPOSE) down --remove-orphans -v
	@echo "${RED}Stack stopped with error code $1${RESET}"
	@exit 1
.is-stack-running:
	@if [ -z "$$(docker ps -q -f name=oc-stack)" ]; then echo "${RED}The oc-stack is not running...${RESET}"; exit 1; fi