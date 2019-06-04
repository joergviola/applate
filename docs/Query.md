# Query API

_This is work in progress!_

Applate provides an API for querying for data.  
This is done via this endpoint:

``api`` here always refers to your API endpoint, eg. 
`https://yourdomain.com/api/v1.0`.

````
POST <api>/{type}/query

{query}
````
or using the JavaScript API
````
api.find(type, query)
````

## Where-Clause

Currently, only `AND`` queries are implemented:
````
{
    and: {
        field-name: value,
        field-name: {operator: op, value: val},
        field-name: value,
        ...
    }
}               
````
Each clause can have a simple form, resulting in an equals clause, 
or have an operator. Currenty implemented:

|Example|SQL|
|---|---|
|``field: value``|``field=value``|
|``field: {'=': value}``|``field=value``|
|``field: {'in': array}``|``field in (array)``|

## With-Joins

In order to fetch referenced items in one query, 
you specify with-clauses:
````
{
    with: {
        target-field: {one: type, this: from},
        target-field: {many: type, that: from},

        ...
    }
}               
````
In each result item, a `target-field`is added, holding 
a full item of ``type``, the `id` of which is read 
from ``from``.

## Example

The following example will return all users.
On each user item, besides to ``role_id``field, you will also find 
a `role` field, holding the full role object referenced
by ``role_id`` as well as a ``rights`` array, holding all ``rights`` 
for the table ``users``. The rights themselves will again hold their 
``role`` object (which is of course stupid and only meant as an example ;-): 

````
POST <api>/users/query

{
  "and": {
  },
  "with": {
    "rights": {
      "many": "right",
      "this": "role_id",
      "that": "role_id",
      "query": {
         "and": {
            "tables": "users"
         },
         "with": {
    "role": {
      "one": "role",
      "this": "role_id"
    }
         }
      }
    },
    "role": {
      "one": "role",
      "this": "role_id"
    }
  }
}
````

Result:

````
[
  {
    "id": 4,
    "name": "admin",
    "email": "admin",
    "email_verified_at": null,
    "remember_token": null,
    "created_at": null,
    "updated_at": null,
    "client_id": 7,
    "role_id": 8,
    "rights": [
      {
        "id": 1,
        "client_id": 7,
        "role_id": 8,
        "tables": "users",
        "columns": "*",
        "where": "**",
        "actions": "CR",
        "role": {
          "id": 8,
          "client_id": 7,
          "name": "Admin3333"
        }
      }
    ],
    "role": {
      "id": 8,
      "client_id": 7,
      "name": "Admin3333"
    }
  }
]````




