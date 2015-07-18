<?php
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceImpl.php');
require_once (__DIR__ . '/../anyem.bsrv.impl/ResourceHolderImpl.php');

/**
 * @author Anis.Halayem
 */
class ResourceHolderTst {
    public static function main() {
        $resource  = new ResourceImpl("anyem.com", "toolkit", "a", 50);
        $resource1 = new ResourceImpl("anyem.com", "toolkit", "b", 24);
        $resource2 = new ResourceImpl("anyem.com", "toolkit", "c", 65);
        $resource3 = new ResourceImpl("anyem.com", "toolkit", "d", 45);
        $resource4 = new ResourceImpl("anyem.com", "toolkit", "e", 12);
        
        $resourceHolder = new ResourceHolderImpl(ResourceHolderImpl::_GET, $resource);
        $resourceHolder = new ResourceHolderImpl(ResourceHolderImpl::_GET, $resource1);
        $resourceHolder = new ResourceHolderImpl(ResourceHolderImpl::_GET, $resource2);
        $resourceHolder = new ResourceHolderImpl(ResourceHolderImpl::_GET, $resource3);
        $resourceHolder = new ResourceHolderImpl(ResourceHolderImpl::_GET, $resource4);
        
        
        $resourceHolder = new ResourceHolderImpl(ResourceHolderImpl::_PUT, $resource);
        $resourceHolder = new ResourceHolderImpl(ResourceHolderImpl::_PUT, $resource4);
        var_dump($resourceHolder->getResourcePool());
         
        
    }
}
ResourceHolderTst::main();
