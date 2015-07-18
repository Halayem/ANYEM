<?php
require_once (__DIR__ . '/../anyem.utils/AnyemConfigReaderimpl.php');
require_once (__DIR__ . '/../anyem.utils/IAnyemDateTimeUtils.php');
/**
 * Description of AnyemUtils
 *
 * @author Anis.Halayem
 */

class AnyemDateTimeUtilsImpl implements IAnyemDateTimeUtils{
    private static $_initialized    = FALSE;
    
    public static function init() {
        if (self::$_initialized === FALSE) {
            $anyemConfigReader  = new AnyemConfigReaderImpl (__DIR__ . '/../config/anyem_config_shared.properties');
            date_default_timezone_set($anyemConfigReader->readConfig(self::DEFAULT_TIME_ZONE));
            self::$_initialized = TRUE;
        }
    }
}
AnyemDateTimeUtilsImpl::init();