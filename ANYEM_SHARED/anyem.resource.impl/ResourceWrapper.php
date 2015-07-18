<?php

/**
 *
 * @author Anis.Halayem
 */
class ResourceWrapper {
    /**
     *
     * @var string  
     */
    private $_action;
    
    /**
     *
     * @var ResourceImpl
     */
    private $_resource;
    
    public function __construct ($resource, $action) {
        $this->_resource =   $resource  ;
        $this->_action   =   $action    ;
    }
    
    public function getAction() {
        return $this->_action;
    }
    public function getResource() {
        return $this->_resource;
    }
}
