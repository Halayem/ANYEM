<?php
require_once (__DIR__ . '/../anyem.utils/IAnyemConfigReader.php');

/**
 * Description of AnyemConfigReader
 *
 * @author Anis.Halayem
 */
class AnyemConfigReaderImpl implements IAnyemConfigReader {
    private $_config_a = array ();
    
    public function __construct($file) {
        $this->loadConfig($file);
    }
    private function loadConfig ($file) {
        $cfg_line_s = ''  ;
        if (!is_file($file)) {
            throw new Exception(sprintf("can not find this file: %s", $file));
        }
        
        $f_cfg = fopen ($file, 'r') ;
        if (!$f_cfg) {
            throw new Exception($file);
        }
        while ( $cfg_line_s = fgets ($f_cfg)) {
            if (
                (
                    (strpos (trim ($cfg_line_s), self::CFG_COMMENT) !== FALSE) &&
                    (strpos (trim ($cfg_line_s), self::CFG_COMMENT) == 0)
                ) ||
                    (strlen (trim ($cfg_line_s)) == 0)
                ) {                                                                                                     // donc c'est une ligne commentaire
                continue ;
            }
            $tokens_a = explode (self::CFG_DELIMITER, $cfg_line_s);
            if ($tokens_a === FALSE) {
                continue ; 
            }
            
            $this->_config_a[trim ($tokens_a[0])] = trim ($tokens_a[1]) ;
            if (count ($tokens_a) > 2) {
                $this->continueReading($tokens_a[0], $tokens_a);
            }
        }
    }

    public function readConfig ($param, $default = NULL) {
        if      (!isset($this->_config_a[$param])) { return $default; }
        else                                       { return $this->_config_a[$param] ; }
    }
    
    private function continueReading ($param, $tokens_a) {
        for ($i = 2 ; $i < count ($tokens_a) ; $i++) {
            $this->_config_a[trim ($param )] += self::CFG_DELIMITER + trim ($tokens_a[i]);
        }
    }
}