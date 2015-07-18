# ANYEM
PHP - Synchronized Data Structure Server 

ANNYEM is an open source project, written in PHP, mainly for PHP applications.
The main objective for this project is to have the possibility to have a 'static synchronized' variables for PHP
Also, to share variables between any PHP Applications.

ANYEM is a Client/Server Application

ANYEM_SERVER : Written in PHP and it is based on Socket, it holds data in memory, and use Judy Array.

ANYEM_CLIENT : A client for PHP that offers for the developer to do :
GET     : reserve a variable and obtains his value
PUT     : unreserve a variable and push a new value
DELETE  : delete the Key/Value from ANYEM_SERVER
RELEASE : unreserve a variable without changing his value
READ    : read the content of the variable from ANYEM_SERVER
