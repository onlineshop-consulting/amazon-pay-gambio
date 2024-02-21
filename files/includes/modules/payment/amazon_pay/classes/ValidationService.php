<?php

namespace OncoAmazonPay;

use Exception;
use OncoAmazonPay\Exceptions\AddressRestrictionException;
use OncoAmazonPay\Exceptions\ConfigException;
use OncoAmazonPay\Exceptions\CreateSessionException;
use OncoAmazonPay\Exceptions\GetSessionException;
use OncoAmazonPay\Exceptions\InitializeClientException;
use OncoAmazonPay\Exceptions\InvalidKeyException;

class ValidationService
{

    protected $configurationService;
    protected $apiService;

    public function __construct()
    {
        $this->configurationService = new ConfigurationService();
        $this->apiService = new ApiService();
    }

    public function validate(): array
    {
        $isSuccess = false;
        $exceptionMessage = '';
        $message = APC_VALIDATION_SUCCESS;
        $level = 'error';
        try {
            $this->validateCredentials();
            $this->validatePrivateKey();
            $this->initializeClient();
            $this->createSession();
            $isSuccess = true;
        } catch (ConfigException $e) {
            $exceptionMessage = $message = APC_VALIDATION_CREDENTIALS_INCOMPLETE;
        } catch (InvalidKeyException $e) {
            $message = APC_VALIDATION_INVALID_KEY;
        } catch (InitializeClientException $e) {
            $message = APC_VALIDATION_INITIALIZE_CLIENT;
            $exceptionMessage = $e->getMessage();
        } catch (CreateSessionException $e) {
            $message = APC_VALIDATION_CREATE_SESSION;
            $exceptionMessage = $e->getMessage();
        } catch (AddressRestrictionException $e) {
            $isSuccess = true;
            $message = APC_VALIDATION_ADDRESS_RESTRICTION;
            $exceptionMessage = APC_VALIDATION_ADDRESS_RESTRICTION . ': ' . $e->getMessage();
            $level = 'warning';
        }
        return [
            'success' => $isSuccess,
            'message' => $message,
            'level' => $level,
            'exceptionMessage' => $exceptionMessage,
        ];
    }

    public function validateCredentials()
    {
        $configuration = $this->configurationService->getConfiguration(true);
        if (empty($configuration->getMerchantId())) {
            throw new ConfigException('merchantId');
        }

        if (empty($configuration->getPublicKeyId())) {
            throw new ConfigException('publicKeyId');
        }

        if (empty($configuration->getClientId())) {
            throw new ConfigException('storeId');
        }
    }

    protected function validatePrivateKey()
    {
        $keyContent = file_get_contents($this->configurationService->getPrivateKeyPath());
        if ((strpos($keyContent, 'BEGIN RSA PRIVATE KEY') === false) && (strpos($keyContent, 'BEGIN PRIVATE KEY') === false)) {
            throw new InvalidKeyException();
        }
    }

    /**
     * @throws InitializeClientException
     */
    protected function initializeClient()
    {
        $this->apiService->getClient(true);
    }

    /**
     * @throws AddressRestrictionException
     * @throws CreateSessionException
     * @throws GetSessionException
     */
    protected function createSession()
    {
        try {
            $checkoutService = new CheckoutService();
            $session = $checkoutService->createCheckoutSession(true);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'provided for PublicKeyId is invalid') !== false) {
                $msg = 'Die eingegebene Public Key ID ist nicht gültig';
            } elseif (strpos($msg, 'provided for \'storeId\'') !== false) {
                $msg = 'Die eingegebene Store-ID ist nicht gültig';
            } elseif (strpos($msg, 'Unable to verify signature') !== false) {
                $msg = 'Die eingegebene Public Key ID gehört nicht zum Public Key. Bitte eine neue Public Key ID in der Integration Central erzeugen.';
            }

            throw new CreateSessionException($msg);
        }


        if (empty($session->getCheckoutSessionId())) {
            throw new CreateSessionException(print_r($session, true));
        }
        try {
            $this->apiService->getClient(true)->getCheckoutSession($session->getCheckoutSessionId());
        } catch (Exception $e) {
            $msg = $e->getMessage();
            throw new GetSessionException($msg);
        }

//        try {
//            $this->apiService->createCheckoutSession(false, null, true, true);
//        } catch (\Exception $e) {
//            throw new AddressRestrictionException($e->getMessage());
//        }
    }
}