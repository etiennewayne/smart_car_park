<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Park;
use Curl;
use Auth;


class PaymongoController extends Controller
{


    public function pay(Request $req){
        $user = Auth::user();
        $amount = ($req->hours * 20)*100; 
        $parkName = "Parking Space No:".$req->park+1;
        $data = [
            'data' => [
                'attributes' => [
                    "billing" => [ //this is for billing email
                        "email" => $user->email,
                        "name" => $user->fname.' '.$user->mname.' '.$user->lname ,
                        "phone" => $user->contact_no
                    ],
                    'line_items' =>[
                        [
                            'currency'      =>'PHP',
                            'amount'        => $amount,
                            'description'   =>'Payment for parking for '.$req->hours.' hours.', 
                            'name'          =>$parkName,
                            'quantity'      =>1,
                        ]
                    ],
                
                    'payment_method_types' =>[
                        'card','gcash'
                    ],
                    'success_url' => 'http://127.0.0.1:8000/paymongo/success', //we will change this with our domain name <127.0.0.1>
                    'cancel_url' => 'http://127.0.0.1:8000/paymongo/cancel',
                    'description'   =>'Parking fee payment for '.$req->hours.' hour(s).',
                    'send_email_receipt' => true //set true
                ],
            ]
        ];
        
        $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions')
            ->withHeader('Content-Type: application/json')
            ->withHeader('accept: application/json')
            ->withHeader('Authorization: Basic '.env('AUTH_PAY'))
            ->withData($data)
            ->asJson()
            ->post();

            //dd($response);
            \Session::put('session_id',$response->data->id);

            return redirect()->to($response->data->attributes->checkout_url);
        
    }


    public function success(){

        $sessionID = \Session::get('session_id');

        $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions/'.$sessionID)
            ->withHeader('accept: application/json')
            ->withHeader('Authorization: Basic '.env('AUTH_PAY'))
            ->asJson()
            ->get();
        $itemName = $response->data->attributes->line_items[0]->name;
        preg_match('/(\d+)/', $itemName, $parkRow);


        Park::where('park_id', $parkRow)
            ->update([
                'is_occupied' => 1,
            ]);
            return redirect('/home');



    }
}