# Business Logic

_This is work in progress!_

Applate is primarily made as a [JAMstack](https://jamstack.org/)
template where business logic is implemented in client-side JavaScript.

Nevertheless, sometimes serside logic is required.
This is done via events & listeners.
Create a class eg ```MySubscriber``` in `backend/app/Listeners`.
Such a class may have four methods:


## handleQuery

```handleQuery(ApiQueryEvent $event)``` is called after 
a query (whether the single item GET or the one from the
Query API) took place. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|items|Result of the query|

You are allowed to change the query items.  

## handleCreate

```handleCreate(ApiCreateEvent $event)``` is called after 
an item is created. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|item|Item created|
|id|Id of the item created|

## handleUpdate

```handleUpdate(ApiUpdateEvent $event)``` is called before 
an item is created. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|item|Item to be updated|
|id|Id of the item to be updated|

## handleDelete

```handleCreate(ApiCreateEvent $event)``` is called before 
an item is deleted. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|id|Id of the item de be deleted|
