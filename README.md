# slim and symfony/messenger for sync and async messages handling

This is a simple example of how to use `slim` and `symfony/messenger` together to be used as `sandbox` for all team to play and understand how to work with `slim` and `symfony/messenger`. More details on the Sequence Diagram below

> A `command` or `query` can be dispatched to any bus (sync and async) so you can decide the behaviour depending on your use case flow.

## Requirements
- Docker and Docker Compose
- GNU bash 
- GNU Make

## Usage
To start the project you need to run the following commands:

```sh
make build
make start
make follow-logs # to see the logs 
make consume  # run the symfony/messenger consumer 'bin/console messenger:consume async'
make stop
```
> You will have the rabbitmq administration interface available at `http://0.0.0.0:15672` with the following credentials `user:password`.


## Notes
- src/Infrastructure/MessageBus/CommandBus.php: `CommandBus` with an async transport configuration.
- src/Infrastructure/MessageBus/QueryBus.php: `QueryBus` with an sync transport configuration.
- src/Infrastructure/MessageBus/EventBus.php: `EvenBus` with an async transport configuration for DomainEvents for example.
- src/Infrastructure/MessageBus/BusTransport.php: Configuration for the async transport.
- src/Application/Command/ : Folder with all the commands.
- src/Application/Query/ : Folder with all the queries.
- app/settings : To know all the settings you could tweak with the `symfony/messenger` component.

> symfony/messenger comes with a bundle consumer inside the library so we don't have to write any consumer for the async transport.
> to run the consumer 

## Sequence Diagram

`async` transport works as `"Fire and Forget"`, so the handler will dispatch the command and return immediately.

`sync` transport works as `"Wait and Return"`, so the handler will dispatch the command and wait for the response (normally query handling is always sync).

`bin/console messenger:consume async --time-limit=3600 --memory-limit=128M` to run the async consumer (already bundle inside the symfony?messenger library).


```plantuml

participant "Controller" as controller
participant "UseCase" as use_case
participant "QueryBus (sync)" as query_bus
participant "QueryHandler" as query_handler
participant "CommandBus (async)" as command_bus
queue "Queue" as queue
control "consumer" as consumer
participant "CommandHandler" as command_handler

controller -> use_case: execute()
use_case -> query_bus: dispatch(Query)
query_bus -> query_handler: handles(Query)
query_handler -> use_case: return(QueryResponse)
use_case -> controller: return(QueryResponse)
use_case --> command_bus: dispatch(Command)
activate use_case #lightblue
command_bus --> queue: push(Command)
queue <-- consumer: consume(Command)
consumer --> command_handler: handles(Command)
deactivate use_case
note right of controller
  The execution will finish before the command is handled
  due the nature of the async transport
end note
controller <- use_case: end()

```