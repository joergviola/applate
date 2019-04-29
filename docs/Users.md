# Users, Roles and Rights

Applate imposes a simple access model: 

* Each user has exaclty one role.
* Each role has several rights in an ordered list.
* An action on a type is only allowed when a right exists that permits that access.
* The right may impose further constraints for the action (like columns and rows).  

Each of these three entities can be accessed throught the usual [RESTful API](REST.md)

## Clients

Each record in applate is assigned to exaclty one client.
No user is able to make any requests outside of her client.

## Users

A user is authenticated by her e-mail and password during [Authentication](Authentication.md).
Each subsequent request is then checked against the rights of her role.

## Roles

Roles are simply lists of rights.

## Rights

Each rights has the following attributes:

|Attribute|Description|Example|
|---|---|---|
|tables|The types this right applies to. If "*", all types are applicable.|users,role|
|columns|The fields of the type this right applies to. If "*", all fields are applicable.|*|
|where|An expression imposed on the items in the scope of the action.|TBD|
|actions|A combination of the letters CRUD|R|

For example:

|tables|columns|where|actions|description|
|---|---|---|---|---|
|user|*|NULL|CRUD|Kann alles mit user machen|
|role,user|name|NULL|R|darf nur lesen und bekommt nur die Spalte name
|role|name|NULL|U|darf nur die Spalte name ändern
|*|*|TBD: org_id=user.org_id|CRUD|Kann nur Sätze mit der eigenen org_id lesen und ändern

