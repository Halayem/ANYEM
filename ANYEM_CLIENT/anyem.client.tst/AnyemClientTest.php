<?php
set_time_limit (0) ;

/**
 * 
 * @author Anis.Halayem
 */
class AnyemClientTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceIdentifierImpl.php');
        require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php') ;

        require_once (__DIR__ . '/../anyem.client.impl/ClientConnectionImpl.php');
        require_once (__DIR__ . '/../anyem.client.impl/AnyemClientImpl.php');
    }
    
    public function testGetPut() {
        $expected = $iteration = 2000;
        
        $clientConnection   = ClientConnectionImpl::newClient();
        $identifier         = new ResourceIdentifierImpl("anyem.com", "anyemNameSpace", "a");
        $anyemClient        = new AnyemClientImpl($clientConnection, $identifier);
        
        $a = 0;
        for ($i=0 ; $i<$iteration ; $i++) {
            try {
                $responseWrapper = $anyemClient->get($a, 10, 300000);
                
            }catch (Exception $e) {
                print $e->getMessage() . "\n";
                continue;
            }

            $a = $responseWrapper->getResource()->getData();
            $anyemClient->put(++$a);
        }
        $this->assertEquals($expected, $a);
    }
}