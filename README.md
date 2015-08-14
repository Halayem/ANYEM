`ANYEM : PHP Synchronized Data Structure Server

 ==============================================`

1. INTRODUCTION
---------------
**ANYEM** is an open source project, written in *PHP*.

The main objective of this project is to give the possibility for PHP Developers to :
* Share variables between different PHP instances;
* Share variables between different PHP Applications;
* Synchronize access for variables; 

**ANYEM_SERVER**
Written in PHP and it is based on Socket.

It holds data in memory, and use [Judy Array](https://github.com/orieg/php-judy).
Below is the exhaustive list of operations that ANYEM_SERVER can treat :

Operation | Type | Description
--- | --- | ---
GET | synchronized | reserve a variable and returns his value;
PUT | synchronized | unreserve a variable and update his new value;
DELETE | synchronized | delete the variable from ANYEM_SERVER;
RELEASE | synchronized | unreserve a variable without changing his value;
READ | not synchronized | the value of the variable from ANYEM_SERVER;

**ANYEM_CLIENT**

PHP Package that contains classes to interact with **ANYEM_SERVER**

2. HOW TO INSTALL
-----------------

**ANYEM_SERVER**

1. Download both: *ANYEM_SERVER* and *ANYEM_SHARED* and place them in the same directory;
2. **ANYEM_SERVER requirements**:
  1. Log4PHP : You can use the version delivered in the package or download [Log4PHP](https://logging.apache.org/log4php/download.html) from Apache Server
  2. Judy    : You can build Judy for Linux or Windows Server by following the documentation from the official PHP web site:
[How to install Judy](http://php.net/manual/en/judy.installation.php), or download the compiled DLL for Windows from the official PECL website:
[Judy DLL](https://pecl.php.net/package/Judy)
Do not forget to update your *php.ini* by adding judy library to the list of PHP Extension
3. **ANYEM_SERVER configuration**:
This configuration is pretty simple:
  1. Go to : `ANYEM_SHARED/config` and edit the file `anyem_config_shared.properties`
  
     `port`                       : designates the port on which *ANYEM_SERVER* will listen for connections;

     `maxResourceSerializedLength`: designates the maximum number of bytes read;
4. **Environment configuration**:
Add PHP installation directory to your PATH environment variable

Now, start your **ANYEM_SERVER** from command line 
`php ANYEM_SERVER/anyem.app.server.impl/ServerImpl.php`
You should see the dump of the configuration used by the server and these three informations messages:
  1. socket created successfully...
  2. socket binded successfully...
  3. socket listening successfully...

Thats all for the server :)

**ANYEM_CLIENT**

1. Download both: **ANYEM_CLIENT** and **ANYEM_SHARED** and place them in the same directory;
2. **ANYEM_CLIENT** requirements:
  1. Log4PHP : You can use the version delivered in the package or download [Log4PHP](https://logging.apache.org/log4php/download.html) from Apache Server
3. **ANYEM_CLIENT** configuration:
  1. You must copy the file that has been configured in 
     `ANYEM_SERVER/ANYEM_SHARED/config/anyem_config_shared.properties`, 
     Paste it in 
     `ANYEM_CLIENT/ANYEM_SHARED/config` 
     and keep the same file name
  2. Go to `ANYEM_CLIENT/config` and edit `anyem_config.properties`
     `address`            : designates the ANYEM_SERVER address
     `defaultMaxAttempt`  : default value that designates the maximum number of attempts that the client can made to reserve a variable,vthis value can be changed by code;
     `defaultDelayAttempt`: default value that designates the delay in µSeconds between two reservation attempts, this value can be changed by code;

3. USAGE (EXAMPLES)
       ```php
        require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceIdentifierImpl.php');
        require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php') ;
        require_once (__DIR__ . '/../anyem.client.impl/ClientConnectionImpl.php');
        require_once (__DIR__ . '/../anyem.client.impl/AnyemClientImpl.php');
        // get a new clientConnection Object using ClientConnectionImpl factory
        $clientConnection   = ClientConnectionImpl::newClient();
        // it is important to give an identifier to your object, it will be user as a unique ID by ANYEM_SERVER
        // the constructor of ResourceIdentifierImpl needs 3 parameters tha will help to identify more easily your variables
        $identifier         = new ResourceIdentifierImpl("anyem.com", "anyemNameSpace", "a");
        // now you can construct your AnyemClientImpl object, you can use it to get, put, delete, release and read your variable in your PHP script 
        $anyemClient        = new AnyemClientImpl($clientConnection, $identifier);
        
        // Example of : GET, Update and PUT a variable 
        // this mode is synchronzed, anyone who will attempt to do an update operation of this variable that it is 
        // identified by $identifier object, will wait until you will done
        $a;
        try {
            $responseWrapper = $anyemClient->get($a);
        }
        catch (Exception $e) {
            print $e->getMessage() . "\n";
            continue;
        }
        $a = $responseWrapper->getResource()->getData();
        // will give the content of $a from ANYEM_SERVER
        print_r ($a);
        // update $a
        $a++;
        $anyemClient->put($a);
        // now the variable is released, someone else cas reserve it
        ```
        

