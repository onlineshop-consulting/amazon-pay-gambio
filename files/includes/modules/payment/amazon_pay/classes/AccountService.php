<?php

namespace OncoAmazonPay;


use AddressBlock;
use AmazonPayApiSdkExtension\Struct\Address;
use AmazonPayApiSdkExtension\Struct\Buyer;
use AmazonPayApiSdkExtension\Struct\CheckoutSession;
use CountryService;
use CustomerAdditionalAddressInfo;
use CustomerB2BStatus;
use CustomerCallNumber;
use CustomerCity;
use CustomerCompany;
use CustomerCountryZone;
use CustomerCountryZoneIsoCode;
use CustomerCountryZoneName;
use CustomerDateOfBirth;
use CustomerEmail;
use CustomerFirstname;
use CustomerGender;
use CustomerHouseNumber;
use CustomerInterface;
use CustomerLastname;
use CustomerPassword;
use CustomerPostcode;
use CustomerService;
use CustomerStreet;
use CustomerSuburb;
use CustomerVatNumber;
use Exception;
use Gambio\GX\Application;
use IdType;
use KeyValueCollection;
use MainFactory;
use PDO;
use StaticGXCoreLoader;

class AccountService
{

    const ADDRESS_PLACEHOLDER = '.';
    /**
     * @var LogService
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new LogService();
    }

    public function getStatusId(): int
    {
        return (int)$_SESSION['customers_status']['customers_status_id'];
    }

    public function createAccountSession(Buyer $buyer)
    {
        try {
            $customer = $this->getCustomerByEmail($buyer->getEmail());
            if ($customer) {
                $this->login($customer);
                return $customer->getId();
            }
        } catch (Exception $e) {
            //silent
        }

        /* @var CustomerService $customerService */
        $customerService = StaticGXCoreLoader::getService('Customer');


        $addressBlock = $this->getAddressBlock($buyer->getShippingAddress());
        require_once DIR_FS_INC . 'xtc_create_password.inc.php';
        $password = xtc_create_password(32);

        /** @var CustomerInterface $customer */
        $customer = $customerService->createNewCustomer(
            MainFactory::create(CustomerEmail::class, $buyer->getEmail()),
            MainFactory::create(CustomerPassword::class, $password),
            MainFactory::create(CustomerDateOfBirth::class, '0000-00-00'),
            MainFactory::create(CustomerVatNumber::class, ''),
            MainFactory::create(CustomerCallNumber::class, $buyer->getShippingAddress()->getPhoneNumber() ?: ''),
            MainFactory::create(CustomerCallNumber::class, ''),
            $addressBlock,
            MainFactory::create(KeyValueCollection::class, [])
        );
        $this->login($customer);
        return $customer->getId();
    }

    protected function getCustomerByEmail($email_address)
    {
        $q = "SELECT customers_id FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = ? AND account_type = '0'";
        $result = DbAdapter::fetch($q, [$email_address]);

        if (empty($result)) {
            return null;
        }

        $customerId = new IdType($result['customers_id']);
        $customerService = StaticGXCoreLoader::getService('Customer');
        return $customerService->getCustomerById($customerId);
    }

    protected function login(CustomerInterface $customer)
    {
        if (SESSION_RECREATE == 'True') {
            xtc_session_recreate();
        }

        $_SESSION['customer_id'] = $customer->getId();
        $_SESSION['customer_first_name'] = $customer->getFirstname();
        $_SESSION['customer_last_name'] = $customer->getLastname();
        $_SESSION['customer_default_address_id'] = $customer->getDefaultAddress()->getId();
        $_SESSION['customer_country_id'] = $customer->getDefaultAddress()->getCountry()->getId();
        $_SESSION['customer_zone_id'] = $customer->getDefaultAddress()->getCountryZone()->getId();
        $_SESSION['customer_vat_id'] = $customer->getVatNumber();

        $keys = [
            'customer_id',
            'customer_first_name',
            'customer_last_name',
            'customer_default_address_id',
            'customer_country_id',
            'customer_zone_id',
            'customer_vat_id',
        ];

        foreach ($keys as $key) {
            if (!isset($_SESSION[$key])) {
                continue;
            }
            if (is_object($_SESSION[$key])) {
                if (method_exists($_SESSION[$key], 'asInt')) {
                    $_SESSION[$key] = $_SESSION[$key]->asInt();
                } elseif (method_exists($_SESSION[$key], '__toString')) {
                    $_SESSION[$key] = $_SESSION[$key]->__toString();
                }
            }
        }

        $_SESSION['account_type'] = $customer->isGuest() ? '1' : '0';

        // write customers status in session
        if (method_exists(Application::class, 'updateCustomerInformationInSession')) {
            Application::updateCustomerInformationInSession();
        }

        // restore cart contents
        $_SESSION['cart']->restore_contents();
    }

    protected function getAddressBlock(Address $address): AddressBlock
    {
        $names = $this->getNamesFromString($address->getName());
        $streetAndCompany = $this->getStreetAndCompany($address);

        /** @var CountryService $countryService */
        $countryService = StaticGXCoreLoader::getService('Country');
        $country = $countryService->getCountryByIso2($address->getCountryCode());
        /** @var CustomerCountryZone $countryZone */
        $countryZone = MainFactory::create(CustomerCountryZone::class,
            new IdType(0),
            MainFactory::create(CustomerCountryZoneName::class, ''),
            MainFactory::create(CustomerCountryZoneIsoCode::class, '')
        );

        return MainFactory::create(AddressBlock::class,
            MainFactory::create(CustomerGender::class, ''),
            MainFactory::create(CustomerFirstname::class, $names[0]),
            MainFactory::create(CustomerLastname::class, $names[1]),
            MainFactory::create(CustomerCompany::class, $streetAndCompany[2]),
            MainFactory::create(CustomerB2BStatus::class, (bool)$_SESSION['customer_b2b_status']),
            MainFactory::create(CustomerStreet::class, $streetAndCompany[0]),
            MainFactory::create(CustomerHouseNumber::class, $streetAndCompany[1]),
            MainFactory::create(CustomerAdditionalAddressInfo::class, ''),
            MainFactory::create(CustomerSuburb::class, ''),
            MainFactory::create(CustomerPostcode::class, $address->getPostalCode()),
            MainFactory::create(CustomerCity::class, $address->getCity()),
            $country,
            $countryZone
        );
    }

    protected function getNamesFromString($name): array
    {
        $nameParts = explode(' ', trim($name));
        if (count($nameParts) > 1) {
            $lastName = trim(array_pop($nameParts));
            $firstName = trim(implode(' ', $nameParts));
        } else {
            $lastName = $name;
            $firstName = '.';
        }
        return [$firstName, $lastName];
    }

    protected function getStreetAndCompany(Address $amazonAddress): array
    {
        if (in_array($amazonAddress->getCountryCode(), ['DE', 'AT'])) {
            $streetAndCompany = $this->_getStreetAndCompanyDA($amazonAddress);
        } else {
            $streetAndCompany = $this->_getStreetAndCompany($amazonAddress);
        }
        $street = $streetAndCompany[0];
        $company = $streetAndCompany[1];
        $streetParts = explode(' ', $street);
        if (count($streetParts) > 1) {
            $houseNumber = array_pop($streetParts);
            $street = implode(' ', $streetParts);
        } else {
            $houseNumber = '';
        }
        return [$street, $houseNumber, $company];
    }

    protected function _getStreetAndCompanyDA(Address $amazonAddress): array
    {
        $addressLine1 = trim($amazonAddress->getAddressLine1());
        $addressLine2 = trim($amazonAddress->getAddressLine2());
        $addressLine3 = trim($amazonAddress->getAddressLine3());
        $company = '';
        $street = self::ADDRESS_PLACEHOLDER;
        if ($addressLine2 !== '') {
            if (strlen($addressLine2) < 10 && preg_match('/^\d+/', $addressLine2)) {
                $street = $addressLine1 . ' ' . $addressLine2;
            } else {
                if (preg_match('/\d+/', substr($addressLine1, -2))) {
                    $street = trim($amazonAddress->getAddressLine1());
                    $company = trim($amazonAddress->getAddressLine2());
                } else {
                    $street = trim($amazonAddress->getAddressLine2());
                    $company = trim($amazonAddress->getAddressLine1());
                }
            }
        } elseif ($addressLine1 !== '') {
            $street = $addressLine1;
        } else {
            $this->logger->debug('incomplete address', [$amazonAddress->toArray()]);
        }

        if ($addressLine3 !== '') {
            $company .= ' ' . $addressLine3;
        }

        return [$street, $company];
    }

    protected function _getStreetAndCompany(Address $amazonAddress): array
    {
        $addressLine1 = trim($amazonAddress->getAddressLine1());
        $addressLine2 = trim($amazonAddress->getAddressLine2());
        $addressLine3 = trim($amazonAddress->getAddressLine3());
        $company = '';
        $street = self::ADDRESS_PLACEHOLDER;
        if ($addressLine1 !== '') {
            $street = $addressLine1;
            if ($addressLine2 !== '') {
                if ($this->isHouseNumber($addressLine1) || $this->isHouseNumber($addressLine2)) {
                    $street = $addressLine1 . ' ' . $addressLine2;
                } else {
                    $company = $addressLine2;
                }
            }
            if ($addressLine3 !== '') {
                $company .= ' ' . $addressLine3;
            }
        } elseif ($addressLine2 !== '') {
            $street = $addressLine2;
            if ($addressLine3 !== '') {
                $company = $addressLine3;
            }
        } elseif ($addressLine3 !== '') {
            $street = $addressLine3;
            $this->logger->debug('probably incomplete address', [$amazonAddress->toArray()]);
        } else {
            $this->logger->debug('incomplete address', [$amazonAddress->toArray()]);
        }

        return [$street, $company];
    }

    protected function isHouseNumber($string)
    {
        return preg_match('/^\d+\s*[a-z-]{0,3}$/i', $string);
    }

    public function createGuestSession(CheckoutSession $checkoutSession)
    {
        if (!$checkoutSession->getBuyer() || empty($checkoutSession->getBillingAddress())) {
            return null;
        }

        try {
            $customer = $this->getCustomerByEmail($checkoutSession->getBuyer()->getEmail());
            if ($customer) {
                $this->login($customer);
                $this->setShippingAddressFromCheckoutSession($checkoutSession);
                $this->setBillingAddressFromCheckoutSession($checkoutSession);
                return $customer->getId();
            }
        } catch (Exception $e) {
            //silent
        }

        /* @var CustomerService $customerService */
        $customerService = StaticGXCoreLoader::getService('Customer');
        $addressBlock = $this->getAddressBlock($checkoutSession->getShippingAddress());
        /** @var CustomerInterface $customer */
        $customer = $customerService->createNewGuest(
            MainFactory::create(CustomerEmail::class, $checkoutSession->getBuyer()->getEmail()),
            MainFactory::create(CustomerDateOfBirth::class, '0000-00-00'),
            MainFactory::create(CustomerVatNumber::class, ''),
            MainFactory::create(CustomerCallNumber::class, $checkoutSession->getBillingAddress()->getPhoneNumber() ?: ''),
            MainFactory::create(CustomerCallNumber::class, ''),
            $addressBlock,
            MainFactory::create(KeyValueCollection::class, [])
        );
        $this->login($customer);
        return $customer->getId();
    }

    public function setShippingAddressFromCheckoutSession(CheckoutSession $checkoutSession)
    {
        if (!$this->isLoggedIn()) {
            return;
        }
        $_SESSION['sendto'] = $this->getAddressId($checkoutSession->getShippingAddress());
    }

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['customer_id']);
    }

    public function getAddressId(Address $address, $customerId = null)
    {
        if (empty($customerId)) {
            $customerId = $_SESSION['customer_id'];
        }
        $addressArray = $this->getAddressBookDataArray($address);

        $q = "SELECT * FROM " . TABLE_ADDRESS_BOOK . " WHERE
                            customers_id = ?
                                AND
                            entry_firstname = ?
                                AND
                            entry_lastname = ?
                                AND
                            entry_street_address = ?
                                AND
                            entry_postcode = ?
                                AND
                            entry_city = ?
                                AND 
                            entry_country_id = ?";
        $r = DbAdapter::fetch($q, [
            $customerId,
            $addressArray['entry_firstname'],
            $addressArray['entry_lastname'],
            $addressArray['entry_street_address'],
            $addressArray['entry_postcode'],
            $addressArray['entry_city'],
            $addressArray['entry_country_id'],
        ]);

        if (!empty($r)) {
            return $r["address_book_id"];
        } else {
            $addressArray['customers_id'] = $customerId;
            DbAdapter::insert(TABLE_ADDRESS_BOOK, $addressArray);
            return DbAdapter::lastInsertId();
        }
    }

    public function getAddressBookDataArray(Address $address): array
    {
        $names = $this->getNamesFromString($address->getName());
        $streetAndCompany = $this->getStreetAndCompany($address);

        /** @var CountryService $countryService */
        $countryService = StaticGXCoreLoader::getService('Country');
        $country = $countryService->getCountryByIso2($address->getCountryCode());

        return [
            'entry_firstname' => $names[0],
            'entry_lastname' => $names[1],
            'entry_company' => $streetAndCompany[2],
            'entry_suburb' => '',
            'entry_street_address' => $streetAndCompany[0],
            'entry_house_number' => $streetAndCompany[1],
            'entry_postcode' => $address->getPostalCode(),
            'entry_city' => $address->getCity(),
            'entry_country_id' => $country->getId(),
        ];

    }

    public function setBillingAddressFromCheckoutSession(CheckoutSession $checkoutSession)
    {
        if (!$this->isLoggedIn()) {
            return;
        }
        $_SESSION['billto'] = $this->getAddressId($checkoutSession->getBillingAddress());
    }

    public function getCustomerId(): int
    {
        return (int)$_SESSION['customer_id'];
    }


}