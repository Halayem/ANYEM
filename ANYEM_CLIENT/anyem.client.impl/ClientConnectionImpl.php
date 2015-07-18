<?php
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.utils/AnyemConfigReaderimpl.php');
require_once (__DIR__ . '/../anyem.client.api/IClientConnection.php');

/**
 * Description of ClientConnectionImpl
 *
 * @author Anis.Halayem
 */
class ClientConnectionImpl implements IClientConnection{
    private $_server_addr               ;
    private $_server_port               ;
    private $_max_res_serialized_length ;
    
    public function __construct ($server_addr, $server_port, $max_res_serialized_length) {
        $this->_server_addr                 =   $server_addr;
        $this->_server_port                 =   $server_port;
        $this->_max_res_serialized_length   =   $max_res_serialized_length;
    }
    
    public static function newClient () {
        $anyemConfigReader  = new AnyemConfigReaderImpl (__DIR__ . '/../config/anyem_config.properties')    ;
        $anyemConfigReader2 = new AnyemConfigReaderImpl (__DIR__ . '/../../ANYEM_SHARED/'                   .   
							'config/anyem_config_shared.properties')            ;
        $clientConnection   = new ClientConnectionImpl  ($anyemConfigReader->readConfig  (self::SERVER_ADDRESS),
                                                         $anyemConfigReader2->readConfig (self::SERVER_PORT),
                                                         $anyemConfigReader2->readConfig (self::SERVER_MAX_RESOURCE_SERIALIZED_LENGTH));
        return $clientConnection;
        
    }
    
    public function getServerAddr() {
        return $this->_server_addr;
    }
    public function getServerPort() {
        return $this->_server_port;
    }
    public function getMaxResourceSerializedLength() {
        return $this->_max_res_serialized_length;
    }
}