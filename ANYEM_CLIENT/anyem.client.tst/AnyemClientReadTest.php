<?php

set_time_limit (0) ;

require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceIdentifierImpl.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php') ;

require_once (__DIR__ . '/../anyem.client.impl/ClientConnectionImpl.php');
require_once (__DIR__ . '/../anyem.client.impl/AnyemClientImpl.php');

/**
 * Description of AnyemClientReadTest
 *
 * @author Amina
 */
class AnyemClientReadTest {
    public static function main ($args) {
        $clientConnection   = ClientConnectionImpl::newClient();
        $identifier         = new ResourceIdentifierImpl("anyem.com", "anyemNameSpace", "a");
        $anyemClient        = new AnyemClientImpl($clientConnection, $identifier);
        
        for ($i=0 ; $i<$args[0] ; $i++) {
            usleep(1000);
            try {
                $responseWrapper = $anyemClient->read();
            }
            catch (Exception $e) {
                print $e->getMessage() . "\n";
                continue;
            }
            print sprintf("variable [a] contains: %s\n", $responseWrapper->getResource()->getData());
        }
    }
}
AnyemClientReadTest::main(array(1000));