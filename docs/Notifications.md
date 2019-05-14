# Notifications

A user may listen to operations on an item.

## Listen to operations

Subscriptions are handled by the type 'listen' 
through the usual API. Its properties are:

|Attribute|Description|
|---|---|
|id|Id of the subscription|
|client_id|Client|
|user_id|Id of the listening user|
|type|Type of the item|
|item_id| Id of the item|
|operation|Operation applied: C,U or D|

## Get notifications

To retrieve all current notifications for the signed-in user, simply call

````
GET /notifications
````
or using the JavaScript API
````
api.getNotifications()
````


As a result, a list of notifications with the following attributes is returned:

|Attribute|Description|
|---|---|
|id|Id of the notification|
|client_id|Client|
|user_id|Id of the user listening|
|type|Type of the item|
|item_id| Id of the item|
|operation|Operation applied: C,U or D|

## Clear notifications

After reading the notifications, the user typically want
the to disappear. You can clear the current notifications by

````
DELETE /notifications
````
or using the JavaScript API
````
api.clearNotifications()
````
