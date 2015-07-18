<?php
require_once (__DIR__ . '/../anyem.resource.api/IResponseWrapper.php');

class ResponseWrapperImpl implements IResponseWrapper{
    /**
     *
     * @var ResourceImpl 
     */
    private $_resource      =   NULL    ;
    
    /**
     *
     * @var string 
     */
    private $_error_message  =   NULL    ;
    
    /**
     *
     * @var int 
     */
    private $_response_code  =   NULL    ;
    
    public function setResource ($resource) {
        $this->_resource = $resource ;
    }
    public function setErrorMessage ($error_message) {
        $this->_error_message = $error_message ;
    }
    public function setResponseCode ($response_code) {
        $this->_response_code = $response_code ;
    }
    public function getResource () {
        return $this->_resource;
    }
    public function getErrorMessage () {
        return $this->_error_message;
    }
    public function getResponseCode () {
        return $this->_response_code;
    }
}
