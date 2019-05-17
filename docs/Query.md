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

Currently, only `AND`` queries are implements:
````
{
    and: {
        field-name: value,
        field-name: value,
        field-name: value,
        ...
    }
}               
````

## With-Joins

In order to fetch referenced items in one query, 
you specify with-clauses:
````
{
    with: {
        target-field: {type: type, from: from},
        target-field: {type: type, from: from},
        target-field: {type: type, from: from},
        ...
    }
}               
````
In each result item, a `target-field`is added, holding 
a full item of ``type``, the `id` of which is read 
from ``from``.

## Example

The following example will return all rights for role 23.
On item item, besides to ``role_id``field, you will also find 
a `role` field, holding the full role object referenced
by ``role_id``: 

````
POST <api>/right/query

{
    and: {
        role_id: 23
    },
    with: {
        role: {type:'role', from: 'role_id'}
    }
}
````



