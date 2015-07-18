<?php
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php') ;
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.utils/AnyemConfigReaderimpl.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.logger/apache-log4php-2.3.0/src/main/php/Logger.php');

require_once (__DIR__ . '/ClientImpl.php');

/**
 * Description of AnyemClientImpl
 *
 * @author Anis.Halayem
 */
class AnyemClientImpl extends ClientImpl{
    private static $_DEFAULT_MAX_ATTEMPT    = NULL;
    private static $_DEFAULT_DELAY_ATTEMPT  = NULL;
    private static $_LOG                    = NULL;
    private static $_INITIALIZED            = FALSE;
    
    public static function init() {
        if (self::$_INITIALIZED === FALSE) {
            $anyemConfigReader              = new AnyemConfigReaderImpl      (__DIR__ . '/../config/anyem_config.properties');
            self::$_DEFAULT_MAX_ATTEMPT     = $anyemConfigReader->readConfig (self::DEFAULT_MAX_ATTEMPT);
            self::$_DEFAULT_DELAY_ATTEMPT   = $anyemConfigReader->readConfig (self::DEFAULT_DELAY_ATTEMPT);
            
            Logger::configure (__DIR__ . '/../config/log4php/config.xml') ;
            self::$_LOG                     = Logger::getLogger(__CLASS__);
            
            self::$_INITIALIZED             = TRUE;
        }
    }
    
    /**
     * @param  mixed $data_m
     * @param  int   $maxAttempt, default value is set in configuration file
     * @param  int   $delayAttempt in microseconde, default value is set in configuration file
     * @return ResponseWrapperImpl
     * @throws Exception 
     */

    public function get ($data_m, $maxAttempt = NULL, $delayAttempt = NULL) {
        if (is_null ($maxAttempt))   { $maxAttempt     = self::$_DEFAULT_MAX_ATTEMPT   ; }
        if (is_null ($delayAttempt)) { $delayAttempt   = self::$_DEFAULT_DELAY_ATTEMPT ; }
        
        return $this->action('get', $data_m, $maxAttempt, $delayAttempt);
    }
    
    /**
     * @param  mixed $data_m
     * @param  int   $maxAttempt, default value is set in configuration file
     * @param  int   $delayAttempt in microseconde, default value is set in configuration file
     * @return ResponseWrapperImpl
     * @throws Exception 
     */

    public function release ($maxAttempt = NULL, $delayAttempt = NULL) {
        if (is_null ($maxAttempt))   { $maxAttempt     = self::$_DEFAULT_MAX_ATTEMPT   ; }
        if (is_null ($delayAttempt)) { $delayAttempt   = self::$_DEFAULT_DELAY_ATTEMPT ; }
        
        return $this->action('release', NULL, $maxAttempt, $delayAttempt);
    }
    
    
    /**
     * @param  mixed $data_m
     * @param  int   $maxAttempt, default value is set in configuration file
     * @param  int   $delayAttempt in microseconde, default value is set in configuration file
     * @return ResponseWrapperImpl
     * @throws Exception 
     */

    public function delete ($maxAttempt = NULL, $delayAttempt = NULL) {
        if (is_null ($maxAttempt))   { $maxAttempt     = self::$_DEFAULT_MAX_ATTEMPT   ; }
        if (is_null ($delayAttempt)) { $delayAttempt   = self::$_DEFAULT_DELAY_ATTEMPT ; }
        
        return $this->action('delete', NULL, $maxAttempt, $delayAttempt);
    }
    
    
    public function action ($action, $parameter, $maxAttempt, $delayAttempt) {
        $ma = $maxAttempt;
        
        do {
            /**
             * @var ResponseWrapperImpl
             */
            $responseWrapper = (is_null($parameter) ?  parent::{$action}() : parent::{$action}($parameter)) ;
            if ($responseWrapper->getResponseCode() != ResponseWrapperImpl::SUCCESS_RESPONSE_CODE) { 
                self::$_LOG->info(sprintf("can not <%s>' the resource\n" . 
                                          "response code    : %s\n"      .
                                          "response message : %s",
                                          strtoupper($action),
                                          $responseWrapper->getResponseCode(),
                                          $responseWrapper->getErrorMessage()));
                usleep ($delayAttempt); 
            }
            else {
                self::$_LOG->info (sprintf ("<%s> the resource in attempt n°: %d/%d, total time spent: %d µs",
                                            strtoupper($action),
                                            $maxAttempt - $ma,                     
                                            $maxAttempt,
                                           ($maxAttempt - $ma) * $delayAttempt));
                return $responseWrapper;
            }
        }while (--$ma >= 0);
        
        $errorMsg = sprintf("all attempts are used without releasing the resource (total = %s)", $maxAttempt);
        self::$_LOG->error  ($errorMsg);
        throw new Exception ($errorMsg);
    }
}

AnyemClientImpl::init();