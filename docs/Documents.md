# Documents

You may attach documents to each ressource.
This is done via a seperate Document API.

## Upload documents

````
POST <api>/<type>/<id>/document
````
or using the JavaScript API
````
api.createDocs(type, id, documents)
````
The POST request here is a multipart-request 
containing one part per file.
The ``documents`` parameter is an array of JavaScript 
``File`` objects.


## List documents

````
GET <api>/<type>/<id>/document
````
or using the JavaScript API
````
api.readDocs(type, id)
````
The result is the list of attached documents with the following attributes:

|Attribute|Description|
|---|---|
|id|Id of the document|
|mimetype|Mimetype identified at upload time|
|size|Size in Bytes|
|original|File name at upload time|
|url|URL for downloading the document|

## Remove documents

````
DELETE <api>/<type>/<id>/document
````
or using the JavaScript API
````
api.deleteDocs(type, id, documentIds)
````
The body of the request, and the ``documentIds`` parameter respectively,
is an array of document ids.
