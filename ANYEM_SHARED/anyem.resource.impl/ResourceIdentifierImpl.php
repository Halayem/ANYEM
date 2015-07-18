<?php
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.bsrv.api/IResourceHolder.php');
/**
 * Description of ResourceIdentifierImpl
 *
 * @author Anis.Halayem
 */
class ResourceIdentifierImpl {
    private $_url_s             = NULL  ;
    private $_name_space_s      = NULL  ;
    private $_name_s            = NULL  ;

    /**
     * 
     * @param string $url
     * @param string $name_space
     * @param string $name
     * @throws RuntimeException
     */
    public function __construct($url, $name_space, $name) {
        $resource_identifier_sep = IResourceHolder::_RESOURCE_IDENTIFIER_SEPERATOR;
        if (strpos($url,        $resource_identifier_sep) !== FALSE ||
            strpos($name_space, $resource_identifier_sep) !== FALSE ||
            strpos($name,       $resource_identifier_sep) !== FALSE) {
           
           $errorMsg = "this special string [" . $resource_identifier_sep . "] "    .
                       "can not be used, neither by URL, NAMESPACE or VARIABLE_NAME";
           throw new RuntimeException($errorMsg);
        }
        $this->_url_s               = $url;
        $this->_name_s              = $name;
        $this->_name_space_s        = $name_space;
    }
    
    public function getUrl() {
        return $this->_url_s;
    }
    public function getNamespace() {
        return $this->_name_space_s;
    }
    public function getName() {
        return $this->_name_s;
    }
}