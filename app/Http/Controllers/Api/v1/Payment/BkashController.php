<?php

namespace App\Http\Controllers\Api\v1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\BkashCreatePaymentRequest;
use App\Http\Requests\Payment\BkashPaymentRequest;
use App\Services\PaymentService\BkashPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BkashController extends Controller
{
    protected BkashPaymentService $bkashPaymentService;

    public function __construct(BkashPaymentService $bkashPaymentService)
    {
        $this->bkashPaymentService = $bkashPaymentService;
    }

    /**
     * @throws \Throwable
     */
    public function getToken()
    {
        return $this->bkashPaymentService->getToken();
    }

    public function createPayment(BkashCreatePaymentRequest $request)
    {
        $data = collect($request->all())->except('/' . $request->path())->toArray();
        return $this->bkashPaymentService->createPayment($data);
    }

    public function executePayment(BkashPaymentRequest $request)
    {
        return $this->bkashPaymentService->executePayment($request);
    }

    /**
     * @throws \Throwable
     */
    public function refreshToken()
    {
        return $this->bkashPaymentService->refreshToken();
    }

    public function queryPayment(Request $request)
    {
        $data = collect($request->all())->except('/' . $request->path())->toArray();
        return $this->bkashPaymentService->queryPayment($request);
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
