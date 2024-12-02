# slim and symfony/messenger for sync and async messages handling

This is a simple example of how to use `slim` and `symfony/messenger` together to be used as `sandbox` for all team to play and understand how to work with `slim` and `symfony/messenger`. 

More details on the Sequence Diagram below

> A `command` or `query` can be dispatched to any bus (sync and async) so you can decide the behaviour depending on your use case flow.

## Usage
### Make Targets
To start the project you need to run the following commands:

```sh
make build
make start
make follow-logs # to see the logs 
make stop
make run CMD="{$cmd}" # to run any command inside the container
```

### Console Commands
The symfony messenger commands are available to be used as well in the slim app:

```sh
bin/console messenger:consume {transport} --time-limit={s} --memory-limit={mb} --limit={quantity} # to consume the async messages
bin/console list messenger # show all the commands available under the messenger component in the slim app
```
> For more details about the commands you can check the [symfony messenger documentation](https://symfony.com/doc/current/messenger.html)

## Notes
- `src/Infrastructure/Provider/Messenger`: This folder holds all the classes used to configure the component inside the SLIM app.
- `src/Application/Command/` : Folder with all the commands.
- `src/Application/Query/` : Folder with all the queries.
- `app/settings` : To know all the settings you could tweak with the `symfony/messenger` component.

## Sequence Diagram

`async` bus aka `"Fire and Forget"`: The bus will dispatch the message (`command` or `event`) and return immediately.

`sync` bus aka `"Wait and Return"`: The bus will dispatch the message (`query` or `command`) and wait for the response.


```plantuml

participant "Controller" as controller
participant "UseCase" as use_case
participant "QueryBus (sync)" as query_bus
participant "QueryHandler" as query_handler
participant "CommandBus (async)" as command_bus
queue "Queue" as queue
control "consumer" as consumer
participant "CommandHandler" as command_handler
database "Database" as db

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
alt $command->shouldRetry()
  command_handler -> queue: push(Command)
  queue <-- consumer: consume(Command)
  consumer --> command_handler: handles(Command)
else retries are exhausted
  command_handler -> db: persist(Command)
end
deactivate use_case
note right of controller
  The execution will finish before the command is handled
  due the nature of the async transport
end note
controller <- use_case: end()

```