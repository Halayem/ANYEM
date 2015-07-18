<?php


/**
 *
 * @author Anis.Halayem
 */
interface IAnyemConfigReader {
    const CFG_COMMENT   = '#';
    const CFG_DELIMITER = '=';
    
    /**
     * 
     * @param string $param parameter name
     * @return string parameter value
     */
    public function readConfig($param, $default);
}
