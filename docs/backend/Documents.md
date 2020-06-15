# Documents

You may attach documents to each ressource.
This is done via a seperate Document API.

## Upload documents

````
POST <api>/<type>/<id>/documents
````
or using the JavaScript API
````
api.createDocs(type, id, documents)
````
The POST request here is a multipart-request 
containing one part per file.
The ``documents`` parameter is an object with an array of JavaScript 
``File`` objects under each key. The describes the path where the documents
are to be stored.

If the key ends with ``[]``, this is assumed a multiple upload.
In this case, Files with original name can only exist once.
Otherwise, only one file will be stored under the key.

## List documents

````
GET <api>/<type>/<id>/documents
````
or using the JavaScript API
````
api.getDocs(type, id)
````
The result is the list of attached documents with the following attributes:

|Attribute|Description|Example|
|---|---|---|
|id|Id of the document|4235|
|path|Pfad inkl. Name der Datei|``/cust/avatar.png``
|mimetype|Mimetype identified at upload time|`ìmage/png`
|size|Size in Bytes|7493|
|original|File name at upload time|`my-avatar.png`
|url|URL for downloading the document|`https://domain.test/file/user/565/cust/avatar.png`

## Remove documents

````
DELETE <api>/<type>/<id>/documents
````
or using the JavaScript API
````
api.deleteDocs(type, id, documentIds)
````
The body of the request, and the ``documentIds`` parameter respectively,
is an array of document ids.
