<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PaymentStsPayoneController extends Controller
{

    /*
        * StsPayone Integration
        * Test Card Number 4215375500883243 -- Card 2 = 416639041957036 --  Code 123456

        * http://localhost:8000/sts/checkout?total_amount=1

    /* 
        Inquiry
            * http://localhost:8000/sts/inquiry-action?pun=530032177809315558
        Refund
            * http://localhost:8000/sts/refund-action?total_amount=100&pun=714089268823387865&approval_code=111111
    */

    public function generate_secure_hash(Request $request)
    {
        $credentials = $this->init();

        $pun = rand(100000000000000000, 999999999999999999);
        $sessionId = Session::getId();
        $formatedRequestDate = date('dmYhis');

        $sessionId = Session::getId();

        $PAYONE_SECRET_KEY = $credentials[2];
        $paymentdescription = "description fees payment";
        $parameters = array();
        $parameters['Action'] = '0';
        $parameters['Amount'] = $request->query('total_amount');
        $parameters['BankID'] = $credentials[0];
        $parameters['CurrencyCode'] = '634';
        $parameters['ExtraFields_f14'] = $credentials[3];
        $parameters['Lang'] = 'en';
        $parameters['MerchantID'] = $credentials[1];
        $parameters['MerchantModuleSessionID'] = $sessionId;
        $parameters['PUN'] = $pun;
        $parameters['PaymentDescription'] = urlencode($paymentdescription);
        $parameters['Quantity'] = '1';
        $parameters['TransactionRequestDate'] = $formatedRequestDate;
        ksort($parameters);
        $orderedString = $PAYONE_SECRET_KEY;
        foreach ($parameters as $k => $param) {
            $orderedString .= $param;
        }

        $secureHash = hash('sha256', $orderedString, false);
        $parameters['SecureHash'] = $secureHash;
        $parameters['PG_REDIRECT_URL'] = $credentials[4];

        return redirect(route('redirect.checkout.stspayone', $parameters));
    }

    public function redirect_checkout(Request $request)
    {
        return view('stspayone.payment', ['data' => $request->query()]);
    }

    public function response(Request $request)
    {
        $SECRET_KEY =   env('APP_ENV')  == 'local' ? env('PAYONE_SECRET_KEY_TEST') : env('PAYONE_SECRET_KEY');

        $parameterNames = isset($_REQUEST) ? array_keys($_REQUEST) : [];
        $responseParameters = [];
        foreach ($parameterNames as $paramName) {
            $responseParameters[$paramName] = $_REQUEST[$paramName];
        }

        $receivedSecureHash = $_REQUEST['Response_SecureHash'];

        unset($responseParameters['Response_AgentID']);
        unset($responseParameters['Response_ItemID']);
        unset($responseParameters['Response_SecureHash']);
        unset($responseParameters['Response_StatusMessage']);
        $responseParameters['Response_StatusMessage'] = urlencode($_REQUEST['Response_StatusMessage']);;

        ksort($responseParameters);
        $orderedString = $SECRET_KEY;
        foreach ($responseParameters as $k => $param) {
            $orderedString .= $param;
        }

        $securegenHash = hash('sha256', $orderedString, false);
        if ($receivedSecureHash !== $securegenHash) {
            if ($_REQUEST['Response_Status'] == '0000') {
                $inquiryStatus =  $this->inquiry($transaction->pun, $transaction->school_code);
                // if ($transaction) {
                //     $transaction->status = 'mismatch';
                //     $transaction->approval_code = $inquiryStatus['Response.ApprovalCode'];
                //     $transaction->save();
                // }
            }
            return redirect()->route('payment.error');
        } else {
            $transaction  = TransactionSts::where('pun', $_REQUEST['Response_PUN'])->firstOrFail();
            $dt = new \DateTime();
            $dt->add(new \DateInterval('PT' . 3 . 'H'));
            if ($_REQUEST['Response_Status'] == '0000') {
                $inquiryStatus =  $this->inquiry($transaction->pun, $transaction->school_code);
                // if ($transaction) {
                //     $transaction->status = 'paid';
                //     $transaction->approval_code = $inquiryStatus['Response.ApprovalCode'];
                //     $transaction->save();  
                // }
                return redirect()->route('payment.success', ['pun' => $transaction->pun, 'amount' => $transaction->total_amount / 100, 'status' => 'success', 'date' =>  $dt->format('Y-m-d H:i:s')]);
            } else {
                $transaction->status = $_REQUEST['Response_StatusMessage'];
                $transaction->save();
                return redirect()->route('payment.error', ['pun' => $transaction->pun, 'amount' => $transaction->total_amount / 100, 'status' => 'failed', 'date' => $dt->format('Y-m-d H:i:s')]);
            }
        }
    }

    public function inquiry($pun, $school_code)
    {
        $credentials = $this->init();

        $PAYONE_SECRET_KEY = $credentials[2];

        $parameters = array();
        $parameters['Action'] = '14';
        $parameters['MerchantID'] = $credentials[1];
        $parameters['OriginalPUN'] = $pun;
        $parameters['BankID'] =  $credentials[0];
        $parameters['Lang'] = 'en';

        ksort($parameters);

        $orderedString = $PAYONE_SECRET_KEY;

        foreach ($parameters as $k => $param) {
            $orderedString .= $param;
        }

        $secureHash = hash('sha256', $orderedString, false);

        $inquiryurl = '' . $credentials[5] . '?Action=14&MerchantID=' . $credentials[1] . '&OriginalPUN=' . $pun . '&BankID=' . $credentials[0] . '&Lang=en&SecureHash=' . $secureHash;
        $response = Http::post($inquiryurl)->body();
        $respitems = explode("&", $response);
        $resparray = array();
        foreach ($respitems as $respitem) {
            if (str_contains($respitem, '=')) {
                $resplines = explode("=", $respitem);
                $name = $resplines[0];
                $value = $resplines[1];
                $resparray[$name] = $value;
            }
        }
        return $resparray;
    }

    public function inquiryAction(Request $request)
    {
        $credentials = $this->init();

        $PAYONE_SECRET_KEY = $credentials[2];

        $parameters = array();
        $parameters['Action'] = '14';
        $parameters['MerchantID'] = $credentials[1];
        $parameters['OriginalPUN'] = $request->pun;
        $parameters['BankID'] =  $credentials[0];
        $parameters['Lang'] = 'en';

        ksort($parameters);

        $orderedString = $PAYONE_SECRET_KEY;

        foreach ($parameters as $k => $param) {
            $orderedString .= $param;
        }

        $secureHash = hash('sha256', $orderedString, false);

        $inquiryurl = '' . $credentials[5] . '?Action=14&MerchantID=' . $credentials[1] . '&OriginalPUN=' . $request->pun . '&BankID=' . $credentials[0] . '&Lang=en&SecureHash=' . $secureHash;
        $response = Http::post($inquiryurl)->body();
        $respitems = explode("&", $response);
        $resparray = array();
        foreach ($respitems as $respitem) {
            if (str_contains($respitem, '=')) {
                $resplines = explode("=", $respitem);
                $name = $resplines[0];
                $value = $resplines[1];
                $resparray[$name] = $value;
            }
        }
    }

    public function refund(Request $request)
    {
        $credentials = $this->init();
        $approval_code  = $request->query('approval_code');

        if ($transaction) {
            $pun1 = 'CV' . $approval_code . rand(1000000000, 9999999999);
            $formatedRequestDate = date('dmYhis');
            $PAYONE_SECRET_KEY = $credentials[2];

            $parameters = array();
            $parameters['Action'] = '6';
            $parameters['Amount_1'] = $request->query('total_amount');
            $parameters['BankID'] =  $credentials[0];
            $parameters['CurrencyCode'] = '634';
            $parameters['Lang'] = 'en';
            $parameters['MerchantID'] = $credentials[1];
            $parameters['OriginalTransactionPaymentUniqueNumber_1'] = $request->query('pun');
            $parameters['PUN_1'] = $pun1;
            $parameters['TransactionRequestDate'] = $formatedRequestDate;
            ksort($parameters);

            $orderedString = $PAYONE_SECRET_KEY;

            foreach ($parameters as $k => $param) {
                $orderedString .= $param;
            }

            $secureHash = hash('sha256', $orderedString, false);

            $refundurl = '' . $credentials[5] . '?Action=6&Amount_1=' . $request->query('total_amount') . '&BankID=' . $credentials[0] . '&CurrencyCode=634&Lang=en&MerchantID=' . $credentials[1] . '&OriginalTransactionPaymentUniqueNumber_1=' . $request->query('pun') . '&PUN_1=' . $pun1 . '&TransactionRequestDate=' . $formatedRequestDate . '&SecureHash=' . $secureHash;
            $response = Http::post($refundurl)->body();
            $respitems = explode("&", $response);
            $resparray = array();
            foreach ($respitems as $respitem) {
                if (str_contains($respitem, '=')) {
                    $resplines = explode("=", $respitem);
                    $name = $resplines[0];
                    $value = $resplines[1];
                    $resparray[$name] = $value;
                }
            }
        } else {
            return redirect()->route('payment.error');
        }
    }

    public function init()
    {
        return [
            $bankId = env('APP_ENV')  == 'local' ? env('BankID_STS_TEST') : env('BankID_STS'),
            $marchant    = env('APP_ENV')  == 'local' ? env('MARCHANT_ID_STS_TEST') : env('MARCHANT_ID_STS'),
            $payOneScretKey = env('APP_ENV')  == 'local' ? env('PAYONE_SECRET_KEY_TEST') : env('PAYONE_SECRET_KEY'),
            $responseUrl = env('APP_ENV')  == 'local' ? env('RESPONSE_STS_URL_TEST') : env('RESPONSE_STS_URL'),
            $redirectUrl = env('APP_ENV')  == 'local' ? env('PG_REDIRECT_URL_TEST') : env('PG_REDIRECT_URL'),
            $inquiry_refund_Url = env('APP_ENV') == 'local' ? env('INQUIRY_REFUND_URL_TEST') : env('INQUIRY_REFUND_URL'),
        ];
    }
}
