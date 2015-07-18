<?php

set_time_limit      (0) ;
ini_set             ('memory_limit', '256M');

require_once (__DIR__ . '/../../ANYEM_SHARED/' . '/anyem.resource.impl/ResourceIdentifierImpl.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . '/anyem.resource.impl/ResourceImpl.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php') ;

require_once (__DIR__ . '/../anyem.client.impl/ClientImpl.php');
require_once (__DIR__ . '/../anyem.client.impl/ClientConnectionImpl.php');


class ClientIncrementationTest {
    public static function main ($args) {
        $clientConnection   = ClientConnectionImpl::newClient();
        $identifier         = new ResourceIdentifierImpl("anyem.com", "anyemNameSpace", "a");
        $client             = new ClientImpl($clientConnection, $identifier);
        
        for ($i=0 ; $i < $args[0] ; $i++) {
            $a = 0;
            /**
             * @var ResponseWrapperImpl
             */
            $responseWrapper = $client->get($a);
            $a = $responseWrapper->getResource()->getData();
            $client->put(++$a);
        }
        
        print ("THESE DATA ARE PROVIDED FROM ANYEM SERVER\n");
        printf("after %s repetition, [a] variable contains : %s", $args[0], $a) ;
    }
}
ClientIncrementationTest::main(array(1));









