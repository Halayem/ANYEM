<?php

/**
 *
 * @author Anis.Halayem
 */
interface IServer {
    const SERVER_ADDRESS                        =   "address";
    const SERVER_PORT                           =   "port";
    const SERVER_MAX_BACKLOG                    =   "maxBacklog";
    const SERVER_MAX_RESOURCE_SERIALIZED_LENGTH =   "maxResourceSerializedLength";
    
    public static function start()  ;
    public static function stop()   ;
    public static function server() ;    
}
