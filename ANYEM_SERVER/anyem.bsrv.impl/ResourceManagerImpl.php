<?php

require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResourceWrapper.php')     ;
require_once (__DIR__ . '/../../ANYEM_SHARED/' . 'anyem.resource.impl/ResponseWrapperImpl.php') ;

require_once (__DIR__ . '/../anyem.bsrv.impl/ResourceHolderImpl.php')      ;

/**
 *
 * @author Anis.Halayem
 */
class ResourceManagerImpl {
    
    /**
     * 
     * @param string $resource_wrapper_s
     * @return string serialized response wrapper
     */
    public static function manage ($resource_wrapper_s) {
        /**
         * @var ResourceWrapper 
         */
        $resourceWrapper = unserialize ($resource_wrapper_s);
        /**
         * @var ResponseWrapperImpl
         */
        $responseWrapper = new ResponseWrapperImpl();
        try {
                /**
                 * @var ResourceHolderImpl
                 */
                $resourceHolder =   new ResourceHolderImpl ($resourceWrapper->getAction(), 
                                                            $resourceWrapper->getResource());
                $mixedResponse  =   $resourceHolder->execute();
                if (!is_null($mixedResponse) &&
                    $mixedResponse instanceof ResourceImpl)
                {
                    $responseWrapper->setResource($mixedResponse);
                }
                $responseWrapper->setResponseCode (ResponseWrapperImpl::SUCCESS_RESPONSE_CODE);
        } catch (Exception $ex) {
            $responseWrapper->setResponseCode (ResponseWrapperImpl::ERROR_RESPONSE_CODE);
            $responseWrapper->setErrorMessage ($ex->getMessage());
        }
        return serialize($responseWrapper);
    }
}