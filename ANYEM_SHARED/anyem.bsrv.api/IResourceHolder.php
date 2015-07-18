<?php



/**
 *
 * @author Anis.Halayem
 */
interface IResourceHolder {
    // ***** MUTEX ACTIONS *****
    
    /**
     * return this resource and reserve it
     */
    const _GET      = "GET";
    
    /**
     * unreserve this resource without updating it
     */
    const _RELEASE  = "RELEASE";
    
    /**
     * update this resource and unreserve it
     */
    const _PUT      = "PUT";
    
    /**
     * delete this resource
     */
    const _DELETE   = "DELETE";
    // *************************
    
    /**
     * read only this resource
     */
    const _READ     = "READ";
    
    const _RESOURCE_IDENTIFIER_SEPERATOR = "::";
}
