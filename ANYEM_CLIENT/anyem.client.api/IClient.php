<?php

/**
 *
 * @author Anis.Halayem
 */
interface IClient {
    const CLIENT_DEFAULT_URL                    =   "clientDefaultUrl"              ;
    const CLIENT_DEFAULT_NAMESPACE              =   "clientDefaultNameSpace"        ;
    const CLIENT_HEADER                         =   "HEAD / HTTP/1.0\r\nHost: www.anyem.com\r\nConnection: Close\r\n\r\n"   ;
    
    // -- used by AnyemClientImpl
    const DEFAULT_MAX_ATTEMPT                   =   "defaultMaxAttempt";
    const DEFAULT_DELAY_ATTEMPT                 =   "defaultDelayAttempt";

    /**
     * reserve and get data for the identified resource
     * @param mixed $data_m
     */
    public function get($data_m);
    
    /**
     * release and update data for the identified resource
     * @param mixed $data_m
     */
    public function put($data_m);
    
    /**
     * @return ResponseWrapperImpl
     * @throws Exception
     */
    public function read();
    
    /**
     * @return ResponseWrapperImpl
     * @throws Exception
     */
    public function delete();
    
    
    /**
     * @return ResponseWrapperImpl
     * @throws Exception
     */
    public function release();
    
}
