<?php
require_once (__DIR__ . '/../anyem.resource.api/IResource.php');
require_once (__DIR__ . '/../anyem.resource.impl/ResourceIdentifierImpl.php');
require_once (__DIR__ . '/../anyem.utils/AnyemDateTimeUtilsImpl.php');

/**
 * Description of ResourceImpl
 *
 * @author Anis.Halayem
 */
class ResourceImpl extends ResourceIdentifierImpl {
    /**
     *
     * @var ResourceIdentifierImpl
     */
    private $_resourceIdentifier    = NULL;
    private $_data_m                = NULL  ;
    
    private $_reserved_b            = FALSE ;
    private $_reservation_date      = NULL  ;
    private $_reservation_time      = NULL  ;
    private $_unreservation_date    = NULL  ;
    private $_unreservation_time    = NULL  ;
    
    private $_transaction_id_l      = NULL;
    
    public function __construct ($resourceIdentifier, $data) {
        $this->_resourceIdentifier  = $resourceIdentifier;
        $this->_data_m              = $data;
        $this->_transaction_id_l    = uniqid ("", TRUE);
    }

    public function reserve($transaction_id_l) {
        if ($this->_reserved_b === TRUE) {
            throw new Exception(sprintf ("resource already reserved, by [TransactionId]: <%s>", $this->_transaction_id_l));

        }
        $this->_transaction_id_l    = $transaction_id_l;
        $this->_reserved_b          = TRUE;
        $this->_reservation_date    = date (AnyemDateTimeUtilsImpl::DATE_FORMAT, time());
        $this->_reservation_time    = date (AnyemDateTimeUtilsImpl::TIME_FORMAT, time());
    }
    public function unreserve($transaction_id_l) {
        if ($this->_reserved_b === FALSE) {
            throw new Exception ("resource is already unreserved");
        }
        if ($this->_reserved_b === TRUE &&
            $this->_transaction_id_l !== $transaction_id_l) {
            $errorMessage = "resource reserved in another transaction, unreservation is not permitted\n" .
                            "Reserved by this <TransactionID>              : " . $this->_transaction_id_l . "\n" .
                            "Tried to unreserve it by this <TransactionID> : " . $transaction_id_l        . "\n" ;
            throw new Exception ($errorMessage);
        }
        $this->_reserved_b            = FALSE;
        $this->_transaction_id_l      = NULL;
        $this->_reservation_date      = NULL;
        $this->_reservation_time      = NULL;
        $this->_unreservation_date    = date (AnyemDateTimeUtilsImpl::DATE_FORMAT, time());
        $this->_unreservation_time    = date (AnyemDateTimeUtilsImpl::TIME_FORMAT, time());
    }
    
    /**
     * 
     * @return ResourceIdentifierImpl
     */
    public function getResourceIdenitifier() {
        return $this->_resourceIdentifier;
    }
    
    /**
     * 
     * @param string $data_s
     */
    public function setData($data_s) {
        $this->_data_m = $data_s;
    }
    public function getData() {
        return $this->_data_m;
    }
    
    public function getTransactionId() {
        return $this->_transaction_id_l;
    }
    public function setTransactionId ($transaction_id_l) {
        $this->_transaction_id_l = $transaction_id_l;
    }
    
    public function toString() {
        $str = "\n" .
               "URl: "              . $this->_resourceIdentifier->getUrl()         . ", "      .
               "Name Space: "       . $this->_resourceIdentifier->getNamespace()   . ", "      .
               "Variable Name: "    . $this->_resourceIdentifier->getName()        . ", "      .
               "Data: "             . print_r ($this->_data_m, TRUE)               . "\n"      ;
        return $str;
    }
}