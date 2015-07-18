<?php
set_time_limit      (0) ;
ob_implicit_flush   ( ) ;
ini_set             ('memory_limit', '256M');

require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.utils/AnyemConfigReaderimpl.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceImpl.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceWrapper.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.logger/apache-log4php-2.3.0/src/main/php/Logger.php');

require_once (__DIR__ . '/../anyem.app.server.api/IServer.php');
require_once (__DIR__ . '/../anyem.bsrv.impl/ResourceManagerImpl.php');

class ServerImpl implements IServer {
    private static $_server_addr;
    private static $_server_port;
    private static $_max_backlog;
    private static $_max_res_serialized_length;
    
    private static $_socket;
    private static $_initialized = FALSE;
    
    private static $_log;
    
    private static function init() {
        $anyemConfigReader                  = new AnyemConfigReaderImpl(__DIR__ . '/../config/anyem_config.properties') ;
	$anyemConfigReader2                 = new AnyemConfigReaderImpl(
                                                                        __DIR__ . '/../../ANYEM_SHARED/'                
                                                                        . 
									'config/anyem_config_shared.properties'
                                                                        );
        
        self::$_server_addr                 = $anyemConfigReader->readConfig  (self::SERVER_ADDRESS)                         ;
        self::$_server_port                 = $anyemConfigReader2->readConfig (self::SERVER_PORT)                            ;
        self::$_max_backlog                 = $anyemConfigReader->readConfig  (self::SERVER_MAX_BACKLOG)                     ;
        self::$_max_res_serialized_length   = $anyemConfigReader2->readConfig (self::SERVER_MAX_RESOURCE_SERIALIZED_LENGTH)  ;
        
        Logger::configure (__DIR__ . '/../config/log4php/config.xml') ;
        self::$_log                         = Logger::getLogger(__CLASS__);
        self::$_initialized                 = TRUE;
        
        
        self::$_log->info("server initialized with these configuration : \nSERVER_ADDR                    : " . self::$_server_addr                 .
                                                                        "\nSERVERT_PORT                   : " . self::$_server_port                 . 
                                                                        "\nMAX_BACKLOG                    : " . self::$_max_backlog                 .
                                                                        "\nMAX_RESOURCE_SERIALIZED_LENGTH : " . self::$_max_res_serialized_length   . 
                                                                        "\nSERVER_MEMORY_LIMIT            : " . ini_get("memory_limit")             . "\n\n");
    }
    
    public static function start() {
        if (self::$_initialized === TRUE) {
            $fatalMsg           = "server was already initialized";
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        self::init();
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if ($socket === FALSE) {
            $fatalMsg           = "socket_create, reason : " .  socket_strerror(socket_last_error()) ;
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        self::$_log->info("socket created successfully...");
        
        $r = socket_bind($socket, self::$_server_addr, self::$_server_port);
        if ($r === FALSE) {
            $fatalMsg           = "socket_bind, reason : " .    socket_strerror(socket_last_error($socket)) ;
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        self::$_log->info("socket binded successfully...");
        
        $r = socket_listen($socket, self::$_max_backlog);
        if ($r === FALSE) {
            $fatalMsg           = "socket_listen, reason : " .  socket_strerror(socket_last_error($socket)) ;
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        self::$_log->info("socket listening successfully...");
        
        self::$_socket = $socket;
        self::server();
        self::$_log->info("server started successfully...");
    }
    
    public static function server() {
        do {
            $socket_accept = socket_accept (self::$_socket);
            if ($socket_accept === FALSE) {
                self::$_log->error("socket_accept,reason : " .  socket_strerror(socket_last_error(self::$_socket)) . 
                                   "connection with client will be aborted, but accepting new connections... ") ;
                continue;
            }
            self::$_log->debug("accepting new connection...");
            
            $clientRequest = socket_read ($socket_accept, 
                                          self::$_max_res_serialized_length,
                                          PHP_BINARY_READ);
            if ($clientRequest === FALSE) {
                self::$_log->error("socket_read,reason : " .  socket_strerror(socket_last_error(self::$_socket)) . 
                                   "connection with client will be aborted, but accepting new connections... ") ;
                continue;
            }
            self::$_log->debug("data read successfully...");
            
            $serialized_resource_wrapper_s  = self::extractWrapper($clientRequest);
            self::$_log->debug("received serialized <ResourceWrapper> from client :" . $serialized_resource_wrapper_s);
                
            $serialized_server_response_s   = ResourceManagerImpl::manage($serialized_resource_wrapper_s);
            self::$_log->debug("serialized <ResponseWrapped> that will be sent to client: " . $serialized_server_response_s);
            
            $r = socket_write  ($socket_accept, 
                                $serialized_server_response_s, 
                                strlen($serialized_server_response_s));
            if ($r === FALSE) {
                self::$_log->error("socket_write,reason : " .  socket_strerror(socket_last_error()) . 
                                   "connection with client will be aborted, but accepting new connections... ") ;
                continue;
            }
            self::$_log->debug("serialized <ResponseWrapped> was sent to client: " . $serialized_server_response_s);
            
            // @param2 - 1 => block writing in socket ...
            socket_shutdown ($socket_accept, 1) ;
            socket_close    ($socket_accept)    ;
            
        }while(TRUE);
    }
    
    /**
     * 
     * @param String $clientRequest HEADER & serialized ResourceWrapper
     * @return string serialized resourceWrapper
     * @throws Exception
     */
    private static function extractWrapper ($clientRequest) {
        $t = explode("\r\n\r\n", $clientRequest, 2);
        if (count($t) != 2) {
            $errorMsg           = "bad client request, transaction will be aborted";
            self::$_log->fatal  ($errorMsg);
            throw new Exception ($errorMsg);
        }
        return ($t[1]) ;
    }
    
    public static function stop() {
        if (self::$_initialized === FALSE) {
            $fatalMsg           = "server was already stopped";
            self::$_log->fatal  ($fatalMsg);
            throw new Exception ($fatalMsg);
        }
        socket_close (self::$_socket);
        self::$_initialized = FALSE;
        self::$_log->info ("server stopped successfully...");
    }
}

ServerImpl::start();