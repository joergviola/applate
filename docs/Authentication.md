# Authentication

## Preparation

Authentication provides you with the following:
* Based upon Laravel Passport
* Full fledged OAuth2 Server
* Authentication based on personal access tokens.

During installation, the following command created a client for personal
authentication:
````
$ php artisan passport:client --personal
````
After that you can start 


## Login 

Please keep in mind, that the following process is already implemented in the 
[JavaScript API](api-js.md) library.

On a users login, a token is created with this client that can be used 
to access the REST API. In order to log a user in, perform the following 
request:

````
POST <base>/login
Content-Type: application/json
Accept: application/json

{
    email: <email>, 
    password: <password>
}
````

|Parameter|Description|Example|
|---|---|---|
|base|Base URL of the API|https://yourdomain.com/api/v1.0|
|email|E-Mail of the user|test@test.test|
|password|Password in clear text|supersecret|

|Code|Status|Result|
|---|---|---|
|200|User is logged in|User
|403|User could not be logged in|{message:reason}

On successful login, the authenticated user is return in JSON format.
Its attribute `token` contains the authentication token. 
It is long lived and supposed to be stored securely on the clients
behalf. 

## Authentication

This token can be used on subsequent API call for Bearer Authentication.
Simply send the following header:
````
Authorization: Bearer <token>
````

  

