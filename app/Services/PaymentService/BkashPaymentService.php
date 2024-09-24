<?php

namespace App\Services\PaymentService;

use App\Action\HelperAction;
use App\Helper\Helper;
use App\Models\AppointmentInvoice;
use App\Models\BkashTokenManager;
use App\Models\DoctorAppointment;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\UserWallet;
use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BkashPaymentService
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;

    public function __construct()
    {

//        $bkash_app_key = '4f6o0cjiki2rfm34kfdadl1eqq';
//        $bkash_app_secret = '2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b';
//        $bkash_username = 'sandboxTokenizedUser02';
//        $bkash_password = 'sandboxTokenizedUser02@12345';
//        $bkash_base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized';
//
        $bkash_app_key = 'Da7No1RZf432vCkRBGScPkZ1tc';
        $bkash_app_secret = 'fhR99ulfGA8ZcWrPZu4cL7DLPiBOFOL3cNMKbSfMXAYAf9fVIkEg';
        $bkash_username = '01812442234';
        $bkash_password = '>{W40[@sEpI';
        $bkash_base_url = 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized';

        $this->app_key = $bkash_app_key;
        $this->app_secret = $bkash_app_secret;
        $this->username = $bkash_username;
        $this->password = $bkash_password;
        $this->base_url = $bkash_base_url;
    }

    /**
     * @throws \Throwable
     */
    public function getToken()
    {
        $post_token = array(
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret
        );
        $url = curl_init($this->base_url . "/checkout/token/grant");
        $post_token = json_encode($post_token);
        $header = array(
            'Content-Type:application/json',
            "password:$this->password",
            "username:$this->username"
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $post_token);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultData = curl_exec($url);
        curl_close($url);
        $response = json_decode($resultData, true);
        if (array_key_exists('msg', $response)) {
            return $response;
        }

        if ($response['statusCode'] ?? $response['statusCode'] === '0000') {
            $checkIfExists = BkashTokenManager::query()->first();
            if ($checkIfExists) {
                $checkIfExists->updateOrFail($response);
            } else {
                BkashTokenManager::query()->create($response);
            }
        }
        return HelperAction::successResponse($response['statusMessage'], null);
    }

    /**
     * @throws \Throwable
     */
    public function checkGrantTokenLifeTime()
    {
        $token = BkashTokenManager::query()->latest()->first();
        $updatedAt = strtotime($token->updated_at);
        $currentTimestamp = time();
        $expirationThreshold = 50 * 60;
        if ($updatedAt + $expirationThreshold < $currentTimestamp) {
            $this->getToken();
        }

        $tokenData = $token->fresh();
        return $tokenData->id_token;
    }

    public function createPayment($data)
    {
        try {
            DB::beginTransaction();
            $auth = $this->checkGrantTokenLifeTime();
            $callback = 'https://adbarta.com';
            $callbackURL = $data['path'] ? $callback . $data['path'] : $callback;
            $amount = $data['amount'];
            $payerReference = auth()->user()->phone ?? auth()->user()->email;
            $merchantInvoiceNumber = auth()->user()->uid;
            $userID = auth()->id();
            $wallet = UserWallet::query()->where('user_id', '=', $userID)->firstOrFail();


            $requestBody = array(
                'mode' => '0011',
                'amount' => $amount,
                'currency' => 'BDT',
                'intent' => 'sale',
                'payerReference' => $payerReference,
                'merchantInvoiceNumber' => $merchantInvoiceNumber,
                'callbackURL' => $callbackURL
            );

            $requestBodyJson = json_encode($requestBody);
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . "$auth",
                'X-APP-Key:' . "$this->app_key"
            );
            $url = curl_init("$this->base_url/checkout/create");

            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $requestBodyJson);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            $resultdata = curl_exec($url);
            curl_close($url);
            $obj = json_decode($resultdata);
            if ($obj->statusCode === '0000' || $obj->statusMessage === 'Successful') {

                WalletHistory::query()->create([
                    'user_id' => $userID,
                    'user_wallet_id' => $wallet->id,
                    'name' => auth()?->user()?->full_name,
                    'trxID' => $obj->paymentID,
                    'points' => $amount,
                    'phone' => auth()?->user()?->phone,
                    'gateway' => 'bkashMerchant',
                    'status' => 'pending',
                    'points_type' => 'credit',

                ]);
            }
            DB::commit();
            return $obj;

        } catch (\Throwable $e) {
            Log::error('BKASH TOKEN ERROR' . $e->getMessage());
            DB::rollBack();
            return HelperAction::validationResponse($e->getMessage());
        }
    }

    public function executePayment(Request $request)
    {
        try {

            $auth = BkashTokenManager::query()->latest()->firstOrFail();

            $invoice = WalletHistory::query()
                ->where('user_id', '=', auth()->id())
                ->where('trxID', '=', $request->paymentID)
                ->firstOrFail();

            $token = $auth->id_token;

            $post_token = array(
                'paymentID' => $request->paymentID
            );
            $url = curl_init("$this->base_url/checkout/execute");
            $postToken = json_encode($post_token);

            $header = array(
                'Content-Type:application/json',
                'Authorization:' . trim($token),
                'X-APP-Key:' . "$this->app_key"
            );

            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            $resultdata = curl_exec($url);

            curl_close($url);

            $obj = json_decode($resultdata);

            if ($obj->statusCode === '0000' || $obj->statusMessage === 'Successful') {
                $updateArr['status'] = 'approved';
                $updateArr['phone'] = $obj->payerReference;
                $invoice->updateOrFail($updateArr);
                $wallet = UserWallet::query()->findOrFail($invoice->user_wallet_id);
                $wallet->updateOrFail([
                    'available' => $wallet->available + floatval($invoice->points)
                ]);

            } else {
                $invoice->deleteOrFail();
            }

            return $obj;

        } catch (\Throwable $e) {
            Log::error('BKASH TOKEN ERROR' . $e->getMessage());
            return HelperAction::validationResponse($e->getMessage());
        }
    }

    /**
     * @throws \Throwable
     */
    public function refreshToken()
    {

        $getTokens = BkashTokenManager::query()->latest()->first();
        if (!$getTokens) {
            return $this->getToken();
        }
        $refreshToken = trim($getTokens->refresh_token);
        $post_token = array(
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret,
            'refresh_token' => $refreshToken
        );
        $url = curl_init("https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/refresh");
        $post_token = json_encode($post_token);
        $header = array(
            'Content-Type:application/json',
            "username:$this->username",
            "password:$this->password",

        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $post_token);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultData = curl_exec($url);
        curl_close($url);
        $response = json_decode($resultData, true);

        if (array_key_exists('msg', $response)) {
            return $response;
        }
        if ($response['statusCode'] ?? $response['statusCode'] === '0000') {
            $getTokens->updateOrFail($response);
        }
        return HelperAction::successResponse($response['statusMessage'], null);
    }

    public function queryPayment(Request $request)
    {
        try {
            $token = $this->checkGrantTokenLifeTime();
            $paymentID = array(
                'paymentID' => $request->paymentID
            );
            $requestBodyJson = json_encode($paymentID);

            $url = curl_init("$this->base_url/checkout/payment/status");
            $header = array(
                'Content-Type:application/json',
                'Accept:application/json',
                "authorization:$token",
                "X-APP-Key:$this->app_key"
            );

            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $requestBodyJson);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            $resultData = curl_exec($url);
            curl_close($url);
//            echo $resultData;
            return json_decode($resultData);
        } catch (\Throwable $e) {
            return HelperAction::validationResponse($e->getMessage());
        }

    }

    public function bkashSuccess(Request $request)
    {
        // IF PAYMENT SUCCESS THEN YOU CAN APPLY YOUR CONDITION HERE
        if ('Noman' == 'success') {

            // THEN YOU CAN REDIRECT TO YOUR ROUTE

            Session::flash('successMsg', 'Payment has been Completed Successfully');

            return response()->json(['status' => true]);
        }

        Session::flash('error', 'Noman Error Message');

        return response()->json(['status' => false]);
    }
}
