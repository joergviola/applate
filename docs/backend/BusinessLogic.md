# Business Logic

_This is work in progress!_

Applate is primarily made as a [JAMstack](https://jamstack.org/)
template where business logic is implemented in client-side JavaScript.

Nevertheless, sometimes serverside logic is required.
This is done via events & listeners.
Create a class eg ```MySubscriber``` in `backend/app/Listeners`.
Such a class may have methods listening for events.

On bulk operation, these events are fired for each item.
An exception is the `bulkUpdateOrCreate` API method, with does not fire any events 
(because the decision is made on lower layers of the framework).

Learn more about Events and Listeners by the excellent 
[Laravel documentation](https://laravel.com/docs/7.x/events).

Since gluon is a Laravel application, it is of course possible to extend
the business logic by adding new specific routes and calling these servies directly
from the frontend.

## Login

```handleLogin(ApiAfterLoginEvent $event)``` is called after 
a user is successfully logged in. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User logged in|

You are allowed to change the user.  

## Authentication

Every time, an API request is successfully authenticated, an `ApiAfterAuthenticatedEvent`is thrown 
that you can handle like ```handleAuthentication(ApiAfterAuthenticatedEvent $event)```:

|Field|Content|
|---|---|
|user|Authenticated user|

This event can be used to add context information to the user that can be checked during permission checks,
(see Users)[Users.md].

## Read

```handleQuery(ApiBeforeReadEvent $event)``` is called before 
a query (whether the single item GET or the one from the
Query API) took place. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|query|The query|

You are allowed to change the query.  

```handleQuery(ApiBeforeReadEvent $event)``` is called after 
a query (whether the single item GET or the one from the
Query API) took place. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|items|Result of the query|

You are allowed to change the query items.  

## Create

```handleCreate(ApiBeforeCreateEvent $event)``` is called before 
an item is created. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|item|Item created|

You are allowed to change the item.  

```handleCreate(ApiAfterCreateEvent $event)``` is called after 
an item is created. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|item|Item created|
|id|Id of the item created|

You are allowed to change the item.  

## Update

```handleUpdate(ApiBeforeUpdateEvent $event)``` is called before 
an item is updated. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|item|Item to be updated|
|id|Id of the item to be updated|

You are allowed to change the item.  

```handleUpdate(ApiAfterUpdateEvent $event)``` is called after 
an item is updated. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|count|Items updated|
|id|Id of the item to updated|

## Delete

```handleDelete(ApiBeforeDeleteEvent $event)``` is called before 
an item is deleted. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|id|Id of the item to be deleted|

```handleDelete(ApiBeforeDeleteEvent $event)``` is called before 
an item is deleted. The event provides the following 
information:

|Field|Content|
|---|---|
|user|User performing the action|
|type|Type the action is performed upon|
|id|Id of the item to be deleted|
|count|Items deleted|
