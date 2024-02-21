<?php

namespace AmazonPayApiSdkExtension\Client;

use AmazonPayApiSdkExtension\Struct\Buyer;
use AmazonPayApiSdkExtension\Struct\Charge;
use AmazonPayApiSdkExtension\Struct\ChargePermission;
use AmazonPayApiSdkExtension\Struct\CheckoutSession;
use AmazonPayApiSdkExtension\Struct\DeliveryTracker;
use AmazonPayApiSdkExtension\Struct\PaymentDetails;
use AmazonPayApiSdkExtension\Struct\Refund;
use AmazonPayApiSdkExtension\Exceptions\AmazonPayException;

class Client extends \Amazon\Pay\API\Client
{
    /**
     * @param $checkoutSession
     * @param $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\CheckoutSession|array|string
     * @throws \Exception
     */
    public function createCheckoutSession($checkoutSession, $headers = null)
    {
        if ($checkoutSession instanceof CheckoutSession) {
            $checkoutSession = $checkoutSession->toArray();
        }
        if ($headers === null) {
            $headers = ['x-amz-pay-Idempotency-Key' => uniqid()];
        }
        $result   = parent::createCheckoutSession($checkoutSession, $headers);
        $response = json_decode($result['response'], true);
        if ((int)$result['status'] !== 201) {
            $exception = new AmazonPayException('createCheckoutSession failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
            $exception->setDetails(['checkoutSessionRequestData'=>$checkoutSession]);
            throw $exception;
        }

        return new CheckoutSession($response);
    }

    /**
     * @param string $checkoutSessionId
     * @param null $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\CheckoutSession
     * @throws \Exception
     */
    public function getCheckoutSession($checkoutSessionId, $headers = null)
    {
        $result = parent::getCheckoutSession($checkoutSessionId, $headers);
        $response = json_decode($result['response'], true);
        if ((int)$result['status'] !== 200) {
            throw new AmazonPayException($response['message']);
        }
        return new CheckoutSession($response);
    }

    /**
     * @param string $checkoutSessionId
     * @param \AmazonPayApiSdkExtension\Struct\CheckoutSession|array|string $checkoutSession
     * @param null $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\CheckoutSession|array|bool|string
     * @throws \Exception
     */
    public function updateCheckoutSession($checkoutSessionId, $checkoutSession, $headers = null)
    {
        if ($checkoutSession instanceof CheckoutSession) {
            $checkoutSession = $checkoutSession->toArray();
        }
        $result = parent::updateCheckoutSession($checkoutSessionId, $checkoutSession, $headers);
        //$result['status']
        return new CheckoutSession(json_decode($result['response'], true));
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\DeliveryTracker|array|string $deliveryTracker
     * @param null $headers
     * @return DeliveryTracker
     */
    public function deliveryTrackers($deliveryTracker, $headers = null)
    {
        if ($deliveryTracker instanceof DeliveryTracker) {
            $deliveryTracker = $deliveryTracker->toArray();
        }
        $result = parent::deliveryTrackers($deliveryTracker, $headers);
        if ((int)$result['status'] !== 200) {
            throw new AmazonPayException($result['response']);
        }
        return new DeliveryTracker(json_decode($result['response'], true));
    }

    /**
     * @param string $checkoutSessionId
     * @param \AmazonPayApiSdkExtension\Struct\PaymentDetails|array|string $paymentDetails
     * @param null $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\CheckoutSession
     * @throws \Exception
     */
    public function completeCheckoutSession($checkoutSessionId, $paymentDetails, $headers = null)
    {
        if ($paymentDetails instanceof PaymentDetails) {
            $paymentDetails = $paymentDetails->toArray();
        }
        $result   = parent::completeCheckoutSession($checkoutSessionId, $paymentDetails, $headers);
        $response = json_decode($result['response'], true);
        if ((int)$result['status'] !== 200 && (int)$result['status'] !== 202) {
            throw new AmazonPayException('completeCheckoutSession failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }

        return new CheckoutSession($response);
    }

    /**
     * @param string $chargeId
     * @param null $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\Charge
     * @throws AmazonPayException
     */
    public function getCharge($chargeId, $headers = null)
    {
        $result = parent::getCharge($chargeId, $headers);
        $response = json_decode($result['response'], true);
        if ((int)$result['status'] !== 200) {
            throw new AmazonPayException('getCharge failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }
        return new Charge($response);
    }

    public function captureCharge($chargeId, $charge, $headers = null)
    {
        if ($charge instanceof Charge) {
            $charge = $charge->toArray();
        }
        if ($headers === null) {
            $headers = ['x-amz-pay-Idempotency-Key' => uniqid()];
        }
        $result   = parent::captureCharge($chargeId, $charge, $headers);
        $response = json_decode($result['response'], true);
        if ($result['status'] < 200 || $result['status'] > 299) {
            throw new AmazonPayException('captureCharge failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }

        return new Charge($response);
    }

    /**
     * @param Refund $refund
     * @param null|array $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\Refund
     * @throws AmazonPayException
     */
    public function createRefund($refund, $headers = null)
    {
        if ($refund instanceof Refund) {
            $refund = $refund->toArray();
        }
        if ($headers === null) {
            $headers = ['x-amz-pay-Idempotency-Key' => uniqid()];
        }
        $result   = parent::createRefund($refund, $headers);
        $response = json_decode($result['response'], true);
        if ((int)$result['status'] !== 201) {
            throw new AmazonPayException('createRefund failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }

        return new Refund($response);
    }

    /**
     * @param Charge $charge
     * @param null|array $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\Charge
     * @throws AmazonPayException
     */
    public function createCharge($charge, $headers = null)
    {
        if ($charge instanceof Charge) {
            $charge = $charge->toArray();
        }
        if ($headers === null) {
            $headers = ['x-amz-pay-Idempotency-Key' => uniqid()];
        }
        $result   = parent::createCharge($charge, $headers);
        $response = json_decode($result['response'], true);
        if ($result['status'] < 200 || $result['status'] > 299) {
            throw new AmazonPayException('createCharge failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }

        return new Charge($response);
    }

    /**
     * @param $refundId
     * @param array|null $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\Refund
     * @throws AmazonPayException
     */
    public function getRefund($refundId, $headers = null)
    {
        $result = parent::getRefund($refundId, $headers);
        $response = json_decode($result['response'], true);
        if ((int)$result['status'] !== 200) {
            throw new AmazonPayException('getRefund failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }
        return new Refund($response);
    }

    /**
     * @param $buyerToken
     * @param array|null $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\Buyer
     * @throws AmazonPayException
     */
    public function getBuyer($buyerToken, $headers = null)
    {
        $result = parent::getBuyer($buyerToken, $headers);
        $response = json_decode($result['response'], true);
        if ((int)$result['status'] !== 200) {
            throw new AmazonPayException('getBuyer failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }
        return new Buyer(json_decode($result['response'], true));
    }

    /**
     * @param string $chargePermissionId
     * @param null|array $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\ChargePermission
     * @throws AmazonPayException
     */
    public function getChargePermission($chargePermissionId, $headers = null)
    {
        $result   = parent::getChargePermission($chargePermissionId, $headers);
        $response = json_decode($result['response'], true);
        if ($result['status'] < 200 || $result['status'] > 299) {
            throw new AmazonPayException('getChargePermission failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }

        return new ChargePermission($response);
    }

    /**
     * @param string $chargePermissionId
     * @param \AmazonPayApiSdkExtension\Struct\ChargePermission|array $chargePermission
     * @param null $headers
     *
     * @return \AmazonPayApiSdkExtension\Struct\ChargePermission
     * @throws \Exception
     */
    public function updateChargePermission($chargePermissionId, $chargePermission, $headers = null)
    {
        if ($chargePermission instanceof ChargePermission) {
            $chargePermission = $chargePermission->toArray();
        }
        $result   = parent::updateChargePermission($chargePermissionId, $chargePermission, $headers);
        $response = json_decode($result['response'], true);
        if ($result['status'] < 200 || $result['status'] > 299) {
            throw new AmazonPayException('updateChargePermission failed: ' . $response['message'] . ' - ' . $response['reasonCode']);
        }

        return new ChargePermission($response);
    }

}
