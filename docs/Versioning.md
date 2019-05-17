# Versioning

By default, all types are versioned. Whenever an item
is created, updated or deleted, its status is saved 
with a specific log id and can be restored later on. 

``api`` here always refers to your API endpoint, eg. 
`https://yourdomain.com/api/v1.0`.

## Log listing

Old versions of an item can be retrieved via this call:

````
GET <api>/{type}/{id}/log
````
or using the JavaScript API
````
api.log(type, id)
````

|Parameter|Description|Example|
|---|---|---|
|type|Type of the item|users|
|id|Id of the item|433|

As a result, a list of item with the following attributes is returned:

|Attribute|Description|
|---|---|
|id|Id of the log entry, required for restore|
|client_id|Client|
|created_at|Timestamp of the operation|
|type|Type of the item|
|item_id| Id of the item|
|operation|Operation applied: C,U or D|
|user|The user who applied the operation|
|user_id|Id of the user|
|content|State of the item after the operation|

## Restore

If you have a ``log-id``, you can restore this version of the item by:

````
PUT <api>/{type}/restore/{log-id}
````
or using the JavaScript API
````
api.restore(type, logId)
````

The result is always empty. A new log entry will of course 
be created.
