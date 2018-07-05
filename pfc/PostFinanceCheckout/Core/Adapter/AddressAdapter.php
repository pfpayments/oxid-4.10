<?php
/**
 * PostFinanceCheckout OXID
 *
 * This OXID module enables to process payments with PostFinanceCheckout (https://www.postfinance.ch/).
 *
 * @package Whitelabelshortcut\PostFinanceCheckout
 * @author customweb GmbH (http://www.customweb.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */


namespace Pfc\PostFinanceCheckout\Core\Adapter;
require_once(OX_BASE_PATH . 'modules/pfc/PostFinanceCheckout/autoload.php');

use PostFinanceCheckout\Sdk\Model\AddressCreate;
use PostFinanceCheckout\Sdk\Model\Gender;
use Pfc\PostFinanceCheckout\Core\PostFinanceCheckoutModule;

/**
 * Class AddressAdapter
 * Converts Oxid Address Data into data which can be fed into the PostFinanceCheckout SDK.
 *
 * @codeCoverageIgnore
 */
class AddressAdapter implements IAddressAdapter
{
    private $shipping = null;
    private $billing = null;
    
    /**
     * 
     * @param \oxaddress $shipping
     * @param \oxuser $billing
     */
    public function __construct(\oxsupercfg $shipping = null, \oxuser $billing = null)
    {
        $this->shipping = $shipping;
        $this->billing = $billing;
    }

    public function getShippingAddressData()
    {
        if ($this->shipping) {
            return $this->convertAddress($this->shipping);
        }
        return null;
    }

    public function getBillingAddressData()
    {
        if ($this->billing) {
            return $this->convertAddress($this->billing);
        }
        return null;
    }
    
    /**
     * 
     * @param \oxaddress|\oxuser $address
     * @return \PostFinanceCheckout\Sdk\Model\AddressCreate
     */
    private function convertAddress(\oxsupercfg $address)
    {
    	$addressCreate = new AddressCreate();
        $addressCreate->setGivenName($address->getFieldData('oxfname'));
        $addressCreate->setFamilyName($address->getFieldData('oxlname'));
        $addressCreate->setCity($address->getFieldData('oxcity'));
class_exists('oxcountry');        $country = oxNew('oxcountry');
        /* @var $country \oxcountry */
        if ($country->load($address->getFieldData('oxcountryid'))) {
            $addressCreate->setCountry($country->getFieldData('oxisoalpha2'));
        }
        $addressCreate->setStreet(trim($address->getFieldData('oxstreet') . ' ' . $address->getFieldData('oxstreetnr')));
        $addressCreate->setPhoneNumber($address->getFieldData('oxfon'));
        $addressCreate->setPostalState($address->getFieldData('oxstate'));
        $addressCreate->setPostCode($address->getFieldData('oxzip'));
        $addressCreate->setOrganizationName($address->getFieldData('oxcompany'));
        $addressCreate->setMobilePhoneNumber($address->getFieldData('oxmobfon'));
        $addressCreate->setDateOfBirth(new \DateTime($address->getFieldData('oxbirthdate')));

        $salutation = $address->getFieldData('oxsal');
        if (strtolower($salutation) === 'mr') {
            /** @noinspection PhpParamsInspection */
            $addressCreate->setGender(Gender::MALE);
        } else if (strtolower($salutation) === 'Mrs') {
            /** @noinspection PhpParamsInspection */
            $addressCreate->setGender(Gender::FEMALE);
        }
        $addressCreate->setSalutation($salutation);
        return $addressCreate;
    }

}