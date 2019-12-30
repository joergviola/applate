# Query API

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

Queries consist of nested AND and OR clauses, eg:
````
{
    and: {
        field-name: value,
        field-name: {operator: op, value: val},
        field-name: value,
        or: {
            field-name: value,
            ...
        }
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
|``field: {'>=': value}``|``field>=value``|
|``field: {'<=': value}``|``field<=value``|
|``field: {'>': value}``|``field>value``|
|``field: {'<': value}``|``field<value``|
|``field: {'<>': value}``|``field<>value``|
|``field: {'in': array}``|``field in (array)``|


## Joins
If the query has to expand several types, joins can be used:
````
  "join": {
    "role": {"this":"role_id", "operator":"=", "that":"id"}
  },
````
Fields of joined types are never delivered in the result, 
but they can be queried:
````
  "and": {
    "role.name": "Manager"
  },
````
These joins have nothing in common with the following with-joins.

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

## Paging

To receive only one page of a long result, specify it in the query:
````
{
    page: {
        skip: 10,
        take: 10,
        count: true
    }
}               
````

|Parameter|Description|default|
|---|---|---|
|skip|how many records to skip before first one|nothing skipped|
|take|how many records to return|not limited|
|count|whether to count the whole result|false|

If `count` is set to `true`, the result returned is of the special form

````
{
    count: 534,
    result: [
      ...
    ]
}               
````


## Order

You may specify to order by field:

````
{
    order: {
        sort-field: 'ASC',
        sort-field: 'DESC',
        ...
    }
}               
````

## The `_meta`

Each object queried with a `with` clause is delivered with an `_meta` attribute that contains the `with` clause, along with a `ignore=true` entry. 

This ensures that, sending this exact object to an update action (see [RESTful API](REST.md)), the corresponding object attribute will not be stored into the database (preventing 'unknown column' errors).

For `many` relations, you may set `ignore=false` to sync the relation to the database.

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




