# RESTful API

Applate provides a REST API for access to data. 
You can always get detailed information about the 
API of your respective application by logging into 
the developer portal 
````<baseURL>/login```` and find a 
[swagger](https://swagger.io/ "swagger")-based 
documentation. 

All of these operations are subject to 
[Authorization](Users.md),
[Serverside Business Logic](BusinessLogic.md),
[Versioning](Versioning.md) and
[Notifications](Notifications.md).

``api`` here always refers to your API endpoint, eg. 
`https://yourdomain.com/api/v1.0`.
 

## Create

````
POST <api>/{type}

{item}
````
or using the JavaScript API
````
api.create(type, item)
````

## Read

````
GET <api>/{type}/id
````
or using the JavaScript API
````
api.get(type, id)
````

## Update

````
PUT <api>/{type}/id

{item}
````
or using the JavaScript API
````
api.update(type, id, item)
````

Only attributes mentioned in the `item` are updated.
Please note that you are allowed to change the id 
of an item using this verb. 


## Delete

````
DELETE <api>/{type}/id
````
or using the JavaScript API
````
api.delete(type, id)
````

## Bulk Operations

Each manipulating operation is available as a bulk version:

````
POST <api>/{type}

[{item1}, {item2}, {item3}, ...]
````

````
PUT <api>/{type}

{id1: {item1}, id2: {item2}, ...}
````

````
DELETE <api>/{type}/id1,id2,...
````

Events are triggered per item, so these versions might be expensive!

## Query delete

For delete, you may specify query params in request parameter (since DELETE has no).
The parameters are ANDed. NO EVENTS ARE FIRED!:

````
DELETE <api>/{type}/query?attr1=val1&attr2=val2
````

## `_meta`: Graph Operations

To a limited extend, you can create and update complete object graphs using the API.

If you have eg. an object of type `role`with an attribute `rights` containing an array of objects of type `right`. If you try to store that graph, you will get an error like 'unknown column rights'.

Just add the following:

````
{
    ...,
    _meta: {
        rights: {many: 'right', ignore: false}
    }
}
````

(This structure is already present when the object is the result of a query, just with `ignore:true`).
Updating this will

  1. query for `right` items currently associated to the `role`,
  1. remove old `right` items whose id is not in your `rights` attributes
  1. update `right` items in your `rights` attributes that already have an `id`
  1. create `right` items in your `rights` attributes that have no `id`

The `role_id` field (or whatever you specify using `that`) of the `right` items of course is set correctly.