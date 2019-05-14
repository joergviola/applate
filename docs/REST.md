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
 

## Create

````
POST /{type}

{item}
````
or using the JavaScript API
````
api.create(type, item)
````

## Read

````
GET /{type}/id
````
or using the JavaScript API
````
api.get(type, id)
````

## Update

````
PUT /{type}/id

{item}
````
or using the JavaScript API
````
api.update(type, id, item)
````

Please note that you are allowed to change the id 
of an item using this verb.


## Delete

````
DELETE /{type}/id
````
or using the JavaScript API
````
api.delete(type, id)
````



