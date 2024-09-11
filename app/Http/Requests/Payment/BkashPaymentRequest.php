<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseRequest;
use App\Models\AppointmentInvoice;
use App\Models\Invoice;
use App\Models\WalletHistory;
use Illuminate\Contracts\Validation\Validator;

class BkashPaymentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'paymentID' => 'required|exists:wallet_histories,trxID',
        ];
    }
}
