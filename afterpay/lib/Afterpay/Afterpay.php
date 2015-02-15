<?php
/**
 * AfterPay Class
 * Author: W. Fokkens (me@willemfokkens.com)
 * Version 2.0
 * Date: 2014-12-30
 */


class Afterpay 
{
    // Set variables
    var $authorization;
    var $modus;
    var $order;
	var $order_lines = array();
    var $order_type;
    var $order_type_name;
    var $order_type_function;
    var $order_request;
    var $order_result;
    var $soap_client;
	var $total_order_amount = 0;
    var $wsdl;
    var $country = 'NL';
    var $ordermanagement = false;
    var $ordermanagement_action = null;
	
	// Build AfterPay object
	public function __construct() {
		$this->order = new stdClass();
		$this->order->shopper = new stdClass();
	}
    
    // If order management is used, set action
    public function set_ordermanagement($action) {
        $this->ordermanagement = true;
        $this->ordermanagement_action = $action;
    } 
    
    // create order order information
    public function set_order($order, $order_type) {
		
		// Set order_type, options are B2C, B2B, OM
		$this->set_order_type($order_type);
        
        
        if( $this->order_type == 'OM' ) {
            
            switch($this->ordermanagement_action) {
                case 'capture_full': 
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->capturedelaydays = 0;
                    $this->order->shippingCompany = '';
                    break;
                case 'capture_partial':
                    $this->order->invoicelines = $this->order_lines;
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->capturedelaydays = 0;
                    $this->order->shippingCompany = '';
                    break;
                case 'cancel':
                    $this->order->transactionkey = new stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    break;
                case 'refund_full': 
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->creditInvoicenNumber = $order['creditinvoicenumber'];
                    break;
                case 'refund_partial':
                    $this->order->invoicelines = $this->order_lines;
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->creditInvoicenNumber = $order['creditinvoicenumber'];
                    break;
                case 'void':
                    $this->order->transactionkey = new stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    break;
                default:
                    break;
            }
            
            return;
        }
        
        if( $this->order_type == 'B2C' ) {
            $billto_address = 'b2cbilltoAddress';
            $shipto_address = 'b2cshiptoAddress';
        } elseif ( $this->order_type == 'B2B' ) {
            $billto_address = 'b2bbilltoAddress';
            $shipto_address = 'b2bshiptoAddress';
        }
        
        if( $order['billtoaddress']['isocountrycode'] == 'BE' ) {
            $this->country = 'BE';
        } elseif ( $order['billtoaddress']['isocountrycode'] == 'DE' ) {
            $this->country = 'DE';
        } else {
            $this->country = 'NL';
        }
        
        $this->order->$billto_address = new stdClass();
        $this->order->$shipto_address = new stdClass();
        
        if( $this->order_type == 'B2C' ) {
            $this->order->$billto_address->referencePerson = new stdClass();
		    $this->order->$shipto_address->referencePerson = new stdClass();
        }
		
        $this->order->$billto_address->city = $order['billtoaddress']['city'];
        $this->order->$billto_address->housenumber = $order['billtoaddress']['housenumber'];
        $this->order->$billto_address->isoCountryCode = $order['billtoaddress']['isocountrycode'];
        $this->order->$billto_address->postalcode = $order['billtoaddress']['postalcode'];
        $this->order->$billto_address->streetname = $order['billtoaddress']['streetname'];
        
        $this->order->$shipto_address->city = $order['shiptoaddress']['city'];
        $this->order->$shipto_address->housenumber = $order['shiptoaddress']['housenumber'];
        $this->order->$shipto_address->isoCountryCode = $order['shiptoaddress']['isocountrycode'];
        $this->order->$shipto_address->postalcode = $order['shiptoaddress']['postalcode'];
        $this->order->$shipto_address->streetname = $order['shiptoaddress']['streetname'];
        
        if( $this->order_type == 'B2C' ) {
            $this->order->$billto_address->referencePerson->dateofbirth = $order['billtoaddress']['referenceperson']['dob'];
            $this->order->$billto_address->referencePerson->emailaddress = $order['billtoaddress']['referenceperson']['email'];
            $this->order->$billto_address->referencePerson->gender = $order['billtoaddress']['referenceperson']['gender'];
            $this->order->$billto_address->referencePerson->initials = $order['billtoaddress']['referenceperson']['initials'];
            $this->order->$billto_address->referencePerson->isoLanguage = $order['billtoaddress']['referenceperson']['isolanguage'];
            $this->order->$billto_address->referencePerson->lastname = $order['billtoaddress']['referenceperson']['lastname'];
            $this->order->$billto_address->referencePerson->phonenumber1 = $this->cleanphone( $order['billtoaddress']['referenceperson']['phonenumber'], $order['billtoaddress']['isocountrycode'] );
            
            $this->order->$shipto_address->referencePerson->dateofbirth = $order['shiptoaddress']['referenceperson']['dob'];
            $this->order->$shipto_address->referencePerson->emailaddress = $order['shiptoaddress']['referenceperson']['email'];
            $this->order->$shipto_address->referencePerson->gender = $order['shiptoaddress']['referenceperson']['gender'];
            $this->order->$shipto_address->referencePerson->initials = $order['shiptoaddress']['referenceperson']['initials'];
            $this->order->$shipto_address->referencePerson->isoLanguage = $order['shiptoaddress']['referenceperson']['isolanguage'];
            $this->order->$shipto_address->referencePerson->lastname = $order['shiptoaddress']['referenceperson']['lastname'];
            $this->order->$shipto_address->referencePerson->phonenumber1 = $this->cleanphone( $order['shiptoaddress']['referenceperson']['phonenumber'], $order['billtoaddress']['isocountrycode'] );            
        }

		if( $this->order_type == 'B2B' ) {
            $this->order->company->cocnumber = $order['company']['cocnumber'];
            $this->order->company->companyname = $order['company']['companyname'];
            $this->order->company->vatnumber = $order['company']['vatnumber'];
            
            $this->order->person->dateofbirth = $order['billtoaddress']['referenceperson']['dob'];
            $this->order->person->emailaddress = $order['billtoaddress']['referenceperson']['email'];
            $this->order->person->initials = $order['billtoaddress']['referenceperson']['initials'];
            $this->order->person->isoLanguage = $order['billtoaddress']['referenceperson']['isolanguage'];
            $this->order->person->lastname = $order['billtoaddress']['referenceperson']['lastname'];
            $this->order->person->phonenumber1 = $this->cleanphone( $order['billtoaddress']['referenceperson']['phonenumber'], $order['billtoaddress']['isocountrycode'] );
		}

        $this->order->ordernumber = $order['ordernumber'];
        $this->order->bankaccountNumber = $order['bankaccountnumber'];
        $this->order->currency = $order['currency'];
        $this->order->ipAddress = $order['ipaddress'];
        $this->order->shopper->profilecreated = '2013-01-01T00:00:00';
        $this->order->parentTransactionreference = false;    
        $this->order->orderlines = $this->order_lines;
		$this->order->totalOrderAmount =  $this->total_order_amount;
    }
    
    // Function for creating order lines
	public function create_order_line( $id, $description, $quantity, $unit_price, $vat_category ) {
		$order_line = new stdClass();
		$order_line->articleId = $id;
		$order_line->articleDescription = $description;
		$order_line->quantity = $quantity;
		$order_line->unitprice = $unit_price;
		$order_line->vatcategory = $vat_category;
		
		$this->total_order_amount = $this->total_order_amount + ( $quantity * $unit_price );
		
		$this->order_lines[] = $order_line;
	}
	
	// Process request to SOAP webservice
    public function do_request( $authorization, $modus ) {
        $this->set_modus( $modus );
        $this->set_soap_client();
        $this->set_authorization( $authorization );
        try {
            $this->order_result = $this->soap_client->__soapCall(
                $this->order_type_name, 
            	array(
                	$this->order_type_name => array(  
            	    	'authorization' => $this->authorization, 
            	    	$this->order_type_function => $this->order
                    )
                )
            );
        } catch (Exception $e) {
            $this->order_result = $e;
        }
    }
    
    // Set order types to correct webservice calls and function names
    private function set_order_type($order_type) {
        
        if (!$this->ordermanagement) {
        
            switch( $order_type ) {
                case 'B2C':
                    $this->order_type = 'B2C';
                    $this->order_type_name = 'validateAndCheckB2COrder';
                    $this->order_type_function = 'b2corder';
                    break;
                case 'B2B':
                    $this->order_type = 'B2B';
                    $this->order_type_name = 'validateAndCheckB2BOrder';
                    $this->order_type_function = 'b2border';
                    break;
                default:
                    break;
            }
        } else {
            
            switch($this->ordermanagement_action) {
                case 'capture_full':
                    $this->order_type = 'OM';
                    $this->order_type_name = 'captureFull';
                    $this->order_type_function = 'captureobject';
                    break;
                case 'capture_partial':
                    $this->order_type = 'OM';
                    $this->order_type_name = 'capturePartial';
                    $this->order_type_function = 'captureobject';
                    break;
                case 'cancel':
                    $this->order_type = 'OM';
                    $this->order_type_name = 'cancelOrder';
                    $this->order_type_function = 'ordermanagementobject';
                    break;
                case 'refund_full':
                    $this->order_type = 'OM';
                    $this->order_type_name = 'refundFullInvoice';
                    $this->order_type_function = 'refundobject';
                    break;
                case 'refund_partial':
                    $this->order_type = 'OM';
                    $this->order_type_name = 'refundInvoice';
                    $this->order_type_function = 'refundobject';
                    break;
                case 'void':
                    $this->order_type = 'OM';
                    $this->order_type_name = 'doVoid';
                    $this->order_type_function = 'ordermanagementobject';
                    break;
            }
            
        }
    }
    
    // Set modus, options are test or live
    private function set_modus( $modus ) {
        $this->modus = $modus;
        $this->wsdl = $this->get_wsdl( $this->country, $modus );
    }
    
    // Get correct WSDL endpoint
    private function get_wsdl( $country, $modus ) {
        
        if(!$this->ordermanagement) {
        
            if( $country == 'NL' ) {
                if( $modus == 'test' ) {
                    $wsdl = 'https://test.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                } elseif ( $modus == 'live' ) {
                    $wsdl = 'https://www.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                }
            } elseif ( $country == 'BE' ) {
                if( $modus == 'test' ) {
                    $wsdl = 'https://test.afterpay.be/soapservices/rm/AfterPaycheck?wsdl';
                } elseif ( $modus == 'live' ) {
                    $wsdl = 'https://api.afterpay.be/soapservices/rm/AfterPaycheck?wsdl';
                }
            }
        
        } else {
            
            if( $country == 'NL' ) {
                if( $modus == 'test' ) {
                    $wsdl = 'https://test.acceptgirodienst.nl/soapservices/om/OrderManagement?wsdl';
                } elseif ( $modus == 'live' ) {
                    $wsdl = 'https://www.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                }
            } elseif ( $country == 'BE' ) {
                if( $modus == 'test' ) {
                    $wsdl = 'https://test.afterpay.be/soapservices/om/OrderManagement?wsdl';
                } elseif ( $modus == 'live' ) {
                    $wsdl = 'https://api.afterpay.be/soapservices/om/OrderManagement?wsdl';
                }
            }
        }
        
        return $wsdl;
    }
    
    // Set correct soap client, differs per country
    private function set_soap_client() {
        if ( $this->country == 'NL' ) {
            $this->soap_client = new SoapClient(
                $this->wsdl,
    			array(
    				'trace' => 0,
    		    	'cache_wsdl' => WSDL_CACHE_NONE
    			)
    		);            
        } elseif ($this->country == 'BE' ) {
            $this->soap_client = new SoapClient(
                $this->wsdl,
    			array(
    			    'location' => $this->wsdl,
    				'trace' => 0,
    		    	'cache_wsdl' => WSDL_CACHE_NONE
    			)
    		);
        }
    }
    
    // Set authorisation credentials
    private function set_authorization($authorization) {
        $this->authorization->merchantId = $authorization['merchantid'];
        $this->authorization->portfolioId = $authorization['portfolioid'];
        $this->authorization->password = $authorization['password'];
        //$this->authorization->merchantId = "300005645";
        //$this->authorization->portfolioId = "1";
        ///$this->authorization->password ="17fe96fdff";
    }
	
	// Function for cleaning phone numbers to correct data depending on country
	private function cleanphone( $phonenumber, $country = 'NL' )
	{
		// Replace + with 00
		$phonenumber = str_replace( '+', '00', $phonenumber );
		
		// Remove (0) because output is international format
		$phonenumber = str_replace( '(0)', '', $phonenumber );

		// Only numbers
		$phonenumber = preg_replace( "/[^0-9]/", "", $phonenumber );
		
		// Country specific checks
		if( $country == 'NL' ) {	
			if( strlen( $phonenumber ) == '10' && substr( $phonenumber, 0, 3 ) != '0031' && substr( $phonenumber, 0, 1 ) == '0' ) {
				$phonenumber = '0031' . substr( $phonenumber, -9 ); 
			}
			elseif( strlen( $phonenumber ) == '13' && substr( $phonenumber, 0, 3 ) == '0031') {
				$phonenumber = '0031' . substr( $phonenumber, -9 ); 
			}
		}
		elseif( $country == 'BE' ) {	
			// Land lines
			if( strlen( $phonenumber ) == '9' && substr( $phonenumber, 0, 3 ) != '0032' && substr( $phonenumber, 0, 1 ) == '0' ) {
				$phonenumber = '0032' . substr( $phonenumber, -8 ); 
			}
			elseif(strlen( $phonenumber ) == '12' && substr( $phonenumber, 0, 3 ) == '0032') {
				$phonenumber = '0032' . substr( $phonenumber, -8 ); 
			}
			// Mobile lines
			if( strlen( $phonenumber ) == '10' && substr( $phonenumber, 0, 3 ) != '0032' && substr( $phonenumber, 0, 1 ) == '0' ) {
				$phonenumber = '0032' . substr($phonenumber, -9); 
			}
			elseif( strlen( $phonenumber ) == '13' && substr( $phonenumber, 0, 3) == '0032' ) {
				$phonenumber = '0032' . substr($phonenumber, -9); 
			}			
		}
		
		return $phonenumber;
	}
}