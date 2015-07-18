<?php
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.bsrv.api/IResourceHolder.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceWrapper.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceImpl.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php') ;
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.logger/apache-log4php-2.3.0/src/main/php/Logger.php');

require_once (__DIR__ . '/../anyem.client.api/IClient.php');
require_once (__DIR__ . '/../anyem.client.impl/ClientConnectionImpl.php');

/**
 *
 * @author Anis.Halayem
 */
class ClientImpl implements IClient{
   
    /**
     *
     * @var ClientConnectionImpl 
     */
    private $_clientConnection;
    
    /**
     *
     * @var ResourceIdentifierImpl 
     */
    private $_resourceIdentifier;
    
    /**
     * @var ResourceImpl
     */
    private $_resource;
    
    private static $_log;
    
    private static $_initialized = FALSE;
    public static function init() {
        if (self::$_initialized === TRUE) { 
            return ; 
        }
        Logger::configure (__DIR__ . '/../config/log4php/config.xml') ;
        self::$_log         = Logger::getLogger(__CLASS__);
        self::$_initialized = TRUE;
    }
    
    /**
     * 
     * @param ClientConnectionImpl      $clientConnection
     * @param ResourceIdentifierImpl    $resourceIdentifier
     */
    public function __construct ($clientConnection, $resourceIdentifier) {
        $this->_clientConnection    =   $clientConnection;
        $this->_resourceIdentifier  =   $resourceIdentifier;
    }

    /**
     * 
     * @param string $action
     * @return ResponseWrapperImpl
     * @throws Exception
     */
    private function execute ($action) {
        /**
         * @var ResourceWrapper
         */
        $resourceWrapper    = new ResourceWrapper ($this->_resource, $action);
        $socket             = socket_create (AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === FALSE) {
            $fatalMsg           = "socket_create, reason : " .  socket_strerror (socket_last_error()) ;
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            $fatalMsg           = "socket_set_option, reason : " .  socket_strerror (socket_last_error()) ;
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        
        self::$_log->info("socket created successfully...");
        
        $result             = socket_connect ($socket, 
                                              $this->_clientConnection->getServerAddr(), 
                                              $this->_clientConnection->getServerPort());
        if ($result === FALSE) {
            $fatalMsg           = "socket_connect, reason : " .  socket_strerror (socket_last_error()) ;
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        self::$_log->info("socket connected successfully...");
        
        $clientRequest      = self::CLIENT_HEADER . serialize($resourceWrapper);
        $result2            = socket_write ($socket, 
                                            $clientRequest, 
                                            strlen ($clientRequest));
        if ($result2 === FALSE) {
            $fatalMsg           = "socket_write, reason : " .  socket_strerror (socket_last_error()) ;
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        self::$_log->info   ("socket wrote successfully...");
        self::$_log->debug  ("client wrote this <ResourceWrapper> : " . print_r ($resourceWrapper, TRUE));
        
        $serverResponse = socket_read($socket, 
                                      $this->_clientConnection->getMaxResourceSerializedLength());
        if ($serverResponse === FALSE) {
            $fataMsg = "socket_read,reason : " .  socket_strerror (socket_last_error ($socket));
            self::$_log->error  ($fataMsg);
            throw new Exception ($fataMsg);
        }
        /**
         * var ResponseWrapperImpl
         */
        $responseResourceWrapper = unserialize ($serverResponse);
        self::$_log->debug ("response received from server : " . print_r ($responseResourceWrapper, TRUE))  ;
        
        // @param2 - 0 => block reading in socket ...
        socket_shutdown ($socket, 0);
        socket_close    ($socket)   ;
        unset           ($socket)   ;
        
        self::$_log->info ("connection terminated with server ...");
        
        return $responseResourceWrapper;
    }
    
    /**
     * 
     * @param ResourceIdentifierImpl $resourceIdentifier
     * @param mixed $data_m
     * @return ResponseWrapperImpl Description
     */
    public function get ($data_m) {
        $this->_resource = new ResourceImpl ($this->_resourceIdentifier, $data_m);
        return $this->execute(IResourceHolder::_GET);
    }
    
    /**
     * 
     * @param mixed $data_m
     * @return ResponseWrapperImpl Description
     */
    public function put($data_m) {
        $this->_resource->setData($data_m);
        return $this->execute(IResourceHolder::_PUT);
    }
    
    public function release() {
        return $this->execute(IResourceHolder::_RELEASE);
    }
    
    /**
     * 
     * @return ResponseWrapperImpl
     * @throws Exception
     */
    public function read() {
        $this->_resource = new ResourceImpl ($this->_resourceIdentifier, NULL);
        return $this->execute(IResourceHolder::_READ);
    }
    
    public function delete() {
        $this->_resource = new ResourceImpl ($this->_resourceIdentifier, NULL);
        return $this->execute(IResourceHolder::_DELETE);
    }
}
ClientImpl::init();