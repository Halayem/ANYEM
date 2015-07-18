<?php
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.bsrv.api/IResourceHolder.php');
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.logger/apache-log4php-2.3.0/src/main/php/Logger.php');

/**
 *
 * @author Anis.Halayem
 */
class ResourceHolderImpl implements IResourceHolder {
    /**
     *
     * @var array 
     */
    private static $_RESOURCE_POOL;
    
    /**
     *
     * @var ResourceImpl 
     */
    private $_givenResource;

    /**
     *
     * @var ResourceImpl 
     */
    private $_foundResource;
    
    private $_resource_exist_b;
    
    /**
     *
     * @var string 
     */
    private $_resource_identifer_merged_s;
    /**
     *
     * @var string 
     */
    private $_action;
    
    private static $_log;
    
    private static $_initialized = FALSE;
    public  static function init() {
        if (!self::$_initialized) {
            self::$_RESOURCE_POOL = new Judy(Judy::STRING_TO_MIXED);
            
            self::$_log = Logger::getLogger(__CLASS__);
            self::$_initialized = TRUE;
        }
    }


    public function __construct($action, $givenResource) {
        $this->_action                      = $action;
        $this->_givenResource               = $givenResource;
        /**
         * @var ResourceIdentifierImpl
         */
        $resourceIdentifier                 = $this->_givenResource->getResourceIdenitifier();
        $this->_resource_identifer_merged_s =   $resourceIdentifier->getUrl()           .
                                                self::_RESOURCE_IDENTIFIER_SEPERATOR    .
                                                $resourceIdentifier->getNamespace()     . 
                                                self::_RESOURCE_IDENTIFIER_SEPERATOR    .
                                                $resourceIdentifier->getName()          ;
        $this->resourceLookup();
    }
    
    private function resourceLookup() {
        $resource = &self::$_RESOURCE_POOL[$this->_resource_identifer_merged_s]; 
        
        
        
        if (isset($resource)) 
        {
            $this->_foundResource       = $resource;
            $this->_resource_exist_b    =  TRUE;
            self::$_log->info("Resource <" . $resource->toString() ."> was found in RESOURCE_POOL") ;
        }
        else {
            $this->_foundResource       = NULL;
            $this->_resource_exist_b    = FALSE;
            self::$_log->info("Resource <" . $this->_givenResource->toString() ."> was not found in RESOURCE_POOL") ;
        }
    }
    
   public function execute() {
        switch ($this->_action) {
            case self::_GET : 
            {
                return $this->execGet();
            }
            case self::_PUT : 
            {
                return $this->execPut();
            }
            case self::_RELEASE : 
            {
                $this->execRelease();
                break;
            }
            case self::_READ : {
                return $this->execRead();
            }
            case self::_DELETE : 
            {
                return $this->execDelete();
            }
            default : {
                self::$_log->error(sprintf("unknown action type: %s", $this->_action));
            }
        }
    }
    
    /**
     * 
     * @return string : ResourceImpl data
     */
    private function execRead() {
        if ($this->_resource_exist_b === FALSE) {
            throw new Exception ("requested resource not found");
        }
        return($this->_foundResource) ;
    }
    
    /**
     * 
     * @throws Exception
     */
    private function execPut() {
        if ($this->_resource_exist_b === FALSE) {
            $errorMessage = sprintf("can not execute <%s> on inexistant resource", 
                                    self::_PUT) ;
            self::$_log->error ($errorMessage);
            throw new Exception($errorMessage);
        }
        $this->_foundResource->unreserve ($this->_givenResource->getTransactionId());
        $this->_foundResource->setData   ($this->_givenResource->getData());
        
        self::$_log->info(sprintf("<%s> applied on this [%s] Object: <%s>", 
                                  self::_PUT,
                                  get_class($this->_givenResource),
                                  $this->_foundResource->toString()));
        return NULL;
    }
    
    private function execDelete() {
         if ($this->_resource_exist_b === FALSE) {
            $errorMessage = sprintf("can not execute <%s> on inexistant resource", 
                                    self::_DELETE) ;
            self::$_log->error ($errorMessage);
            throw new Exception($errorMessage);
        }
        $this->deleteEntryResourcePool();
        
        self::$_log->info(sprintf("<%s> applied on this [%s] Object: <%s>", 
                                  self::_DELETE,
                                  get_class($this->_givenResource),
                                  $this->_foundResource->toString()));
        return NULL;
    }

    /**
     * reserve and return the value if resource found
     * else, it will be added to the index and to the resource holder memory
     * @return ResourceImpl
     */
    private function execGet() {
        /**
         * var ResourceImpl
         */
        $resource = NULL;
        if ($this->_resource_exist_b === FALSE) {
            $resource = $this->_givenResource;
        }
        else {
            // WARNING : here i'am manipulating a resource passed by reference from _RESOURCE_POOL !!
            $resource = $this->_foundResource ;
        }
        $resource->reserve($this->_givenResource->getTransactionId());
        $this->updateResourcePool($resource);
        self::$_log->info(sprintf("<%s> applied on this <%s> Object: [%s]",
                                  self::_GET,
                                  get_class($resource),
                                  $resource->toString()));
        return $resource;
    }
    /**
     * 
     * @param ResourceImpl $resource
     */
    private function updateResourcePool($resource) {
        self::$_RESOURCE_POOL[$this->_resource_identifer_merged_s] = $resource;
    }
    
    
    private function deleteEntryResourcePool () {
        unset (self::$_RESOURCE_POOL[$this->_resource_identifer_merged_s]);
    }
    
    
    
    private function execRelease() {
        if ($this->_resource_exist_b === FALSE) {
            $errorMessage = sprintf("can not execute <%s> on inexistant resource", 
                                    self::_RELEASE) ;
            self::$_log->error ($errorMessage);
            throw new Exception($errorMessage);
        }
        $this->_foundResource->unreserve ($this->_givenResource->getTransactionId());
        self::$_log->info(sprintf("<%s> applied on this [%s] Object: <%s>", 
                                  self::_RELEASE,
                                  get_class($this->_givenResource),
                                  $this->_foundResource->toString()));
        return NULL;
    }
    
    public function getResourcePool() {
        return self::$_RESOURCE_POOL;
    }
}

ResourceHolderImpl::init();