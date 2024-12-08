<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    //paypal sandbox intregrate here
    protected $apiUrl;
    protected $clientId;
    protected $secret;

    public function __construct()
    {
        // Set the PayPal API URL based on the mode
        $this->apiUrl = config('services.paypal.mode') === 'sandbox'
            ? 'https://api.sandbox.paypal.com'
            : 'https://api.paypal.com';

        // Set the credentials for PayPal sandbox (or live)
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
    }

    // Step 1: Create Payment (Send request to PayPal to create a payment)
    public function createPayment(Request $request)
    {
        // Get the access token to authenticate with PayPal
        $token = $this->getAccessToken();

        if (!$token) {
            return response()->json(['error' => 'Unable to get PayPal token'], 500);
        }

        // Set the payment data
        $paymentData = [
            'intent' => 'sale',  // Sale is the immediate payment
            'payer' => [
                'payment_method' => 'paypal',
            ],
            'redirect_urls' => [
                'return_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
            ],
            'transactions' => [
                [
                    'amount' => [
                        'total' => $request->input('amount'),  // Amount to charge
                        'currency' => 'USD',
                    ],
                    'description' => $request->input('description'),  // Description of the product/service
                ],
            ],
        ];

        // Send payment request to PayPal API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/v1/payments/payment', $paymentData);

        $payment = $response->json();

        if (isset($payment['links'])) {
            // Retrieve the approval URL for the user to approve the payment
            $approvalUrl = collect($payment['links'])->firstWhere('rel', 'approval_url')['href'];
            return response()->json([
                'paymentID' => $payment['id'],
                'approvalUrl' => $approvalUrl,
            ]);
        } else {
            return response()->json(['error' => 'Payment creation failed', 'message' => $payment]);
        }
    }

    // Step 2: Execute Payment (After user approves payment on PayPal)
    public function executePayment(Request $request)
    {
        $paymentId = $request->input('paymentID');
        $payerId = $request->input('PayerID');

        $token = $this->getAccessToken();
        if (!$token) {
            return response()->json(['error' => 'Unable to get PayPal token'], 500);
        }

        $executionData = [
            'payer_id' => $payerId,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/v1/payments/payment/' . $paymentId . '/execute', $executionData);

        $payment = $response->json();

        if (isset($payment['state']) && $payment['state'] === 'approved') {
            // Save payment details to database
            Payment::create([
                'product_name' => 'Laravel Book',
                'amount' => 50.00,
                'status' => 'success',
            ]);

            return response()->json([
                'message' => 'Payment successful',
                'payment' => $payment,
            ]);
        } else {
            // Save failed payment to database
            Payment::create([
                'product_name' => 'Laravel Book',
                'amount' => 50.00,
                'status' => 'failed',
            ]);

            return response()->json([
                'error' => 'Payment execution failed',
                'message' => $payment,
            ]);
        }
    }

    // Step 3: Cancel Payment
    public function cancelPayment()
    {
        return response()->json(['message' => 'Payment was canceled']);
    }

    // Helper function to get PayPal access token
    private function getAccessToken()
    {
        // Make the token request
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post($this->apiUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        // Retrieve the access token from the response
        $data = $response->json();
        return $data['access_token'] ?? null;
    }
}
