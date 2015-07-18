<?php

/**
 * this is a synchronized incrementation test, 
 * you can launch           ==>    N NUMBER_OF_THREAD
 * each thread will iterate ==>    M TOTAL_ITERATION
 * also, for each launched THREAD, you can configure the    ==> MAX_ATTEMPT
 *                                                          ==> DELAY_ATTEMPT (µs)
 * you can also configure the TOLERANCE_TO_LOSS
 * 2 clients trying to increment 1000 times a shared synchronized resource, 
 * a client can consume all his attempts without getting a lock for this resource
 * so, this resouce will not be incremented.
 * TOLERANCE_TO_LOSS represents this incrementation loss
 */

set_time_limit  (0) ;
ini_set         ('memory_limit', '256M');

require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceIdentifierImpl.php')  ;
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php')     ;

require_once (__DIR__ . '/../anyem.client.impl/ClientConnectionImpl.php');

define ("TOTAL_ITERATION",      100);
define ("NUMBER_OF_THREAD",     20);
define ("MAX_ATTEMPT",          35);
define ("DELAY_ATTEMPT",        100000); # == (1/10) s
define ("TOLERANCE_TO_LOSS",    15);

/**
 * 
 * @author Anis.Halayem
 */
class AnyemClientMultiThreadTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {}
    
    public function testGetPutMultiThread() {
        $startAnyemTest = new AnyemMTForPHPUnit();
        $startAnyemTest->start();
        $startAnyemTest->join();

        $var_pattern_s = 'anyemMultiThreading_';
        for ($i=0 ; $i<NUMBER_OF_THREAD ; $i++) {
            $newVar     = $var_pattern_s + $i;
            ${$newVar}  = new AnyemMT();
            ${$newVar}->start();
            
        }
        for ($i=0 ; $i<NUMBER_OF_THREAD ; $i++) {
            $newVar     = $var_pattern_s + $i;
            ${$newVar}->join();
        }
        
        $endAnyemTest = new AnyemMTForPHPUnit();
        $endAnyemTest->start();
        $endAnyemTest->join();

        $expectedValue  = (TOTAL_ITERATION * NUMBER_OF_THREAD) + $startAnyemTest->value;
        $actualValue    = $endAnyemTest->value;
        $this->assertLessThanOrEqual(
                                     TOLERANCE_TO_LOSS,
                                     $expectedValue - $actualValue
                                    );
    }
}

class AnyemMTForPHPUnit extends Thread {
    private static $_clientConnection   = NULL;
    private static $_identifier         = NULL;
    private static $_initialized        = FALSE;
    
    public $value                       = NULL;
    
    private static function init() {
        if (self::$_initialized === TRUE) {
            return;
        }
        self::$_clientConnection    = ClientConnectionImpl::newClient();
        self::$_identifier          = new ResourceIdentifierImpl("anyem.com", "anyemNameSpace", "a");
        self::$_initialized         = TRUE;
    }


    public function run() {
        self::init();
        require_once (__DIR__ . '/../anyem.client.impl/AnyemClientImpl.php');
        $anyemClient        = new AnyemClientImpl(self::$_clientConnection, self::$_identifier);
        $responseWrapper    = $anyemClient->read();
        $resourceImpl       = $responseWrapper->getResource();
        if (!is_null($resourceImpl)) {
            $value          = $responseWrapper->getResource()->getData();
        }
        else {
            $value          = 0;
        }
        $this->value        = $value;
    }
}

class AnyemMT extends Thread {
    private static $_clientConnection   = NULL;
    private static $_identifier         = NULL;
    private static $_initialized        = FALSE;
    
    private static function init() {
        if (self::$_initialized === TRUE) {
            return;
        }
        self::$_clientConnection    = ClientConnectionImpl::newClient();
        self::$_identifier          = new ResourceIdentifierImpl("anyem.com", "anyemNameSpace", "a");
        self::$_initialized         = TRUE;
    }


    public function run() {
        self::init();
        require_once (__DIR__ . '/../anyem.client.impl/AnyemClientImpl.php');
        $anyemClient = new AnyemClientImpl(self::$_clientConnection, self::$_identifier);
        
        $a = 0;
        for ($i=0 ; $i<TOTAL_ITERATION ; $i++) {
            try {
                $responseWrapper = $anyemClient->get($a, MAX_ATTEMPT, DELAY_ATTEMPT);
            }
            catch (Exception $e) {
                print $e->getMessage() . "\n";
                continue;
            }

            $a = $responseWrapper->getResource()->getData();
            $anyemClient->put(++$a);
        }
    }
}