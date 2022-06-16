<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Traits\GcashTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Checkout;
use App\Customer;
use App\CallbackLogs;
use App\Http\Traits\FormatResponse;

class GcashController extends Controller
{
    use GcashTrait, FormatResponse;

    public function getGcashUser(Request $request)
    {
        
        $data = [
            'auth_code' => $request->input('auth_code')
        ];

        
        $data['route'] = Route::current()->uri();
        $accessToken = $this->getAccessToken($data);
        
        if(!array_key_exists('accessToken', $accessToken)){
            CallbackLogs::create([
                'request' => json_encode($data),
                'response' => json_encode($accessToken)
            ]);

            return $accessToken;
        }
        $userInfo = $this->getUserInfo($data, $accessToken['accessToken']);  
        //return $userInfo; 
        $addressInfo = $this->getUserAddressBook($data, $accessToken['accessToken']);   
        //return $addressInfo;
        if(!array_key_exists('address', $addressInfo)) {
            $addressData[] = '';
        } else {
            $array_address = $addressInfo['address'];
            $data_val = $array_address['data'];
            if(!empty($data_val['address'])) {
                $address = array_values($addressInfo['address']);       
                $addressData = $this->getAddressData($address, $accessToken['accessToken']);
            } else {
                $addressData[] = "";
            }
           
        }        

        
         
        $userDetails = json_decode($userInfo['userInfo']['extendInfo'], true);    
        //return $userDetails;    
        $userCustomer = Customer::where('gcash_number', $userDetails['GCASH_NUMBER'])->first();
        
        //check if the customer already registered in the cms
        if($userCustomer !=  null) {

            $message = [
                'message' => 'Login'
            ];

           return array($message, $userCustomer, $addressData);

        } else {
            $customerDetails = [
                'first_name' => ucwords(strtolower($userDetails['FIRST_NAME'])),
                'last_name' => ucwords(strtolower($userDetails['LAST_NAME'])),
                'phone' => $userDetails['GCASH_NUMBER'],
                'email' => $userDetails['EMAIL_ADDRESS'], 
                'verified_email' => true
            ];

            if(array_key_exists('address', $addressData))  $customerDetails['addresses'] = [ $addressData['address'] ];

            //return $customerDetails;
            $shopify_data = [
                'customer' => $customerDetails
            ];


            $customer = $this->addCustomer($shopify_data);

            if(!array_key_exists('customer', $customer)){
                return $this->errorResponse($customer, 422);
            };

            // return $customer;
            $data1 = $customer['customer'];            
            $id = $data1['id']; 
            
            //return $shopify_data;
            /* generate customer to own database */
            $customers = new Customer;
            $customers->firstname = ucwords(strtolower($data1['first_name']));
            $customers->lastname = ucwords(strtolower($data1['last_name']));
            $customers->external_id = $userDetails['EXTERNAL_ID'];
            $customers->gcash_number = $userDetails['GCASH_NUMBER'];
            $customers->email = $data1['email'];
            $customers->customer_id = $id;
            $customers->save();

            $message = [
                'message' => 'Registered'
            ];

            return array($message, $customers, $addressData);

        }
       
    }

    public function getAddressBook(Request $request)
    {
        //addressbook from glife
        $data = [
            'auth_code' => $request->input('auth_code')
        ];

        
        $data['route'] = Route::current()->uri();
        $accessToken = $this->getAccessToken($data);
        if(!array_key_exists('accessToken', $accessToken)){
            CallbackLogs::create([
                'request' => json_encode($data),
                'response' => json_encode($accessToken)
            ]);

            return $accessToken;
        }
        
        $addressInfo = $this->getUserAddressBook($data, $accessToken['accessToken']);
        //return $addressInfo;
        if(!array_key_exists('address', $addressInfo)) {
            $addressData[] = '';
        } else {
            $address = array_values($addressInfo['address']);       
            //return $address;
            $addressData = $this->getAddressData($address, $accessToken['accessToken']);
        }   
       /*  $address = array_values($addressInfo['address']);

        $addressData = $this->getAddressData($address, $accessToken['accessToken']); */
        
       return $addressData;
    }

    public function inquire(Request $request)
    {
        //inquire payment
        $data = [
            'payment_request_id' => $request->input('payment_request_id')
        ];
        
        $inquirePayment = $this->inquirePayment($data);
        return $inquirePayment;
    }

    public function payment(Request $request)
    {
        //payment
        $data = [                     
            'amount' => $request->input('total_amount'),
            'number' => $request->input('phone'),
            'email' => $request->input('email'),
            'order_id' => $request->input('order_id')
        ];

        $data['route'] = Route::current()->uri();
        $payment = $this->pay($data);
        return $payment;
    }

    public function NotifyPayment(Request $request)
    {
        //callback notification gcash

         $data = [
            "paymentId" => $request->input('paymentId'),
            "paymentRequestId" => $request->input('paymentRequestId'),
            "partnerId" => $request->input('partnerId'),
            "paymentTime" => $request->input('paymentTime'),
            "paymentAmount" => [
                "currency" => $request->input('currency'),
                "value" => $request->input('value'),
            ],
            "paymentStatus" => $request->input('paymentStatus'),
        ]; 

        //return $data;
        $logs = new CallbackLogs;
        $logs->request = json_encode($data);
        $logs->save(); 

        $check = Checkout::where('status', 'pending')->latest('id')->first();
        $req = json_decode($check['request'], true);
        $res = json_decode($check['response'], true);
        $shipping_lines = json_decode($check['shipping_lines'], true);
        
        $req_data = $req['request']; 
        $res_data = $res['checkout'];             
        $line_items = $req_data['line_items'];
        $shipping_address = $req_data['shipping_address'];
        $billing_address = $req_data['billing_address'];
        $total_price = $res_data['total_price'];
        $code = $shipping_lines['id'];
        $price = $shipping_lines['price'];
        $title = $shipping_lines['title'];

        $transactions = [
            [
                'amount' => $total_price,
                'kind' => 'authorization',
                'status' => 'success',
            ]
        ];

        

        if($data['paymentStatus'] == 'SUCCESS') {            
            $orders = [
                'order' => [
                    'email' => $req_data['email'],
                    'customer'=> [
                        'id' => $billing_address['customer_id']
                    ],
                    'financial_status' => 'paid',                   
                     'transactions' => [
                        [
                            'amount' => $total_price,
                            'kind' => 'authorization',
                            'status' => 'success',
                        ]
                    ],
                     'shipping_lines' => [
                        [
                            'code' => $code,
                            'price' => $price,
                            'title' => $title
                        ]
                    ], 
                    'gateway' => 'GCASH Mini Program',
                    'line_items' => $line_items,
                    'shipping_address' => $shipping_address,
                    'billing_address' => $billing_address,                 
                ]
            ]; 

        } else {
            $orders = [
                'order' => [
                    'email' => $req_data['email'],
                    'customer'=> [
                        'id' => $billing_address['customer_id']
                    ],
                    'financial_status' => 'paid',                   
                     'transactions' => [
                        [
                            'amount' => $total_price,
                            'kind' => 'void',
                            'status' => 'failure',
                        ]
                    ],
                    'shipping_lines' => [
                        [
                            'code' => $code,
                            'price' => $price,
                            'title' => $title
                        ]
                    ], 
                    'gateway' => 'GCASH Mini Program',
                    'line_items' => $line_items,
                    'shipping_address' => $shipping_address,
                    'billing_address' => $billing_address,                   
                ]                
            ];
        } 

        $orderCreate= $this->createOrder($orders);
        $orderCreated = $orderCreate['order'];

       if($orderCreated['id'] != null || $orderCreated['id'] != '') {
            $checkout = Checkout::findorFail($check['id']);
            $checkout->update([
                'order_id' => $orderCreated['id'],
                'status' => $orderCreated['financial_status'],
            ]);
       }

        return $checkout;
       
    }
}
