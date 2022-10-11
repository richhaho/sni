<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\CompanySetting;
use Auth;
use App\CardsInfo;
use App\CardTokenize;

class CreditCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
        $company =  CompanySetting::first();
        $cardslist=CardsInfo::where('client_id',Auth::user()->client->id)->get();
        $data = [
        
        'api_key' => $company->apikey,
        'api_secret' => $company->apisecret,
        'js_security_key' => $company->js_security_key,
        'ta_token' => $company->ta_token,
        'payeezy_url' => $company->apiurl,
        'cardslist' => $cardslist,

            ];
        return view('client.creditcard.index', $data);
    }
    public function remove()
    {
        Auth::user()->client->payeezy_type=null;
        Auth::user()->client->payeezy_cardholder_name=null;
        Auth::user()->client->payeezy_exp_date=null;
        Auth::user()->client->payeezy_value=null;
        Auth::user()->client->save();
        return redirect()->route('creditcard.index' );
    }
    public function remove_card(Request $request)
    {
        $id=$request['id'];
        CardsInfo::where('id',$id)->delete();
        return redirect()->route('creditcard.index' );
    }

    public function active_card(Request $request)
    {
        $id=$request['id'];
        $client = Auth::user()->client;
        $card=CardsInfo::where('id',$id)->first();
        if(strlen($client->payeezy_value) > 0){
            $client_payeezy_type=$client->payeezy_type ;
            $client_payeezy_value=$client->payeezy_value ;
            $client_payeezy_cardholder_name=$client->payeezy_cardholder_name ;
            $client_payeezy_exp_date=$client->payeezy_exp_date;

            $client->payeezy_type = $card->payeezy_type;
            $client->payeezy_value = $card->payeezy_value;
            $client->payeezy_cardholder_name = $card->payeezy_cardholder_name;
            $client->payeezy_exp_date = $card->payeezy_exp_date;
            $client->save();

            $card->payeezy_type = $client_payeezy_type;
            $card->payeezy_value = $client_payeezy_value;
            $card->payeezy_cardholder_name = $client_payeezy_cardholder_name;
            $card->payeezy_exp_date = $client_payeezy_exp_date;
            $card->save();


        }else{
            $client->payeezy_type = $card->payeezy_type;
            $client->payeezy_value = $card->payeezy_value;
            $client->payeezy_cardholder_name = $card->payeezy_cardholder_name;
            $client->payeezy_exp_date = $card->payeezy_exp_date;
            $client->save();
            $card->delete();
        }
        
        return redirect()->route('creditcard.index' );
    }
    
    public function tokenize (Request $request) {
       $this->validate($request, [
                'invoice_id' => 'required',
                'currency' => 'required',
                'token' => 'required',
            ]);
       
        $data = $request->all();
        $data['token'] = json_decode($request->token,true);
        $client = Auth::user()->client;

        // $client->payeezy_type = $data['token']['type'];
        // $client->payeezy_value = $data['token']['value'];
        // $client->payeezy_cardholder_name = $data['token']['cardholder_name'];
        // $client->payeezy_exp_date = $data['token']['exp_date'];
        // $client->save();

        $addcard =  CardsInfo::create();
        $addcard->client_id=$client->id;
        $addcard->payeezy_type = $data['token']['type'];
        $addcard->payeezy_value = $data['token']['value'];
        $addcard->payeezy_cardholder_name = $data['token']['cardholder_name'];
        $addcard->payeezy_exp_date = $data['token']['exp_date'];
        $addcard->save();
        
        return json_encode([
                'status' => 'token-save',
                'id' => $data['invoice_id']
                ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function authorizeSession(Request $request)
    {
        $company =  CompanySetting::first();
        $gatewayConfig=[
            "gateway"           => "PAYEEZY",
            "apiKey"            => $company->apikey,
            "apiSecret"         => $company->apisecret,
            "authToken"         => $company->merchant_token,
            "transarmorToken"   => $company->ta_token,
            "zeroDollarAuth"    => true
        ];
        $requestBody=json_encode($gatewayConfig);
        $timestamp=strtotime('now').rand(100,999);
        $nonce=$timestamp.'.'.rand(100,999);
        $msgToSign=$company->apikey.$nonce.$timestamp.$requestBody;
        $msgSignature=$this->genHmac($msgToSign, $company->paymentjs_secret);
         
        
        $headers=array(
            'Api-Key: '           . $company->apikey,
            'Content-Type: '      . 'application/json',
            'Content-Length: '    . strlen($requestBody),
            'Message-Signature: ' . $msgSignature,
            'Nonce: '             . $nonce,
            'Timestamp: '         . $timestamp
        );

        $authorize = curl_init();
        curl_setopt($authorize, CURLOPT_URL, 'https://'.$company->apiurl.'/paymentjs/v2/merchant/authorize-session');
        curl_setopt($authorize, CURLOPT_PORT , 443);
        curl_setopt($authorize, CURLOPT_POST, 1);
        curl_setopt($authorize, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($authorize, CURLINFO_HEADER_OUT, true);
        curl_setopt($authorize, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($authorize, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($authorize, CURLOPT_HEADER, 1);
        $response = curl_exec($authorize);
        $header_size = curl_getinfo($authorize, CURLINFO_HEADER_SIZE);
        curl_close($authorize);
        
        $response_header = substr($response, 0, $header_size);
        $response_body = substr($response, $header_size);

        $response_body=json_decode($response_body);
        $publicKeyBase64=$response_body->publicKeyBase64;

        $header_array = $this->getHeaders($response_header);
        $clientToken=$header_array['Client-Token'];

        $data=[
            'clientToken'     => $clientToken,
            'publicKeyBase64' => $publicKeyBase64,
        ];
        $card_tokenize = CardTokenize::first();
        $card_tokenize->client_id=Auth::user()->client->id;
        $card_tokenize->client_token=$clientToken;
        $card_tokenize->status='authorized';
        $card_tokenize->description='Paymentjs server authorized your tokenize request.';
        $card_tokenize->clients_cardinfo_id=null;
        $card_tokenize->save();


        return response()->json($data);
    }
    public function genHmac($msg, $secret)
    {
        $algorithm = 'sha256';
        $hexEncodedHash = hash_hmac($algorithm, $msg, $secret);
        $base64EncodedHash = base64_encode($hexEncodedHash);
        return $base64EncodedHash;
    }
    public function getHeaders($respHeaders) {
        $headers = array();
        $headerText = substr($respHeaders, 0, strpos($respHeaders, "\r\n\r\n"));
        foreach (explode("\r\n", $headerText) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }


    public function webhook(Request $request)
    {
        $client_token=$request->header('client-token');
        $card_tokenize = CardTokenize::first();
        if ($card_tokenize->client_token != $client_token){
            $card_tokenize->status='failed';
            $card_tokenize->description='Unknown error for tokenize.';
            $card_tokenize->save();
            return;
        }
        $body=$request->all();
        $error_description='';
        if ($body['error']){
            $error_description=$body['gatewayReason'][0]['description'];
            $card_tokenize->status='failed';
            $card_tokenize->description=$error_description;
            $card_tokenize->save();
            return;
        }
        $card=$body['card'];
        
        $payeezy_type=($card['brand']=='american-express') ? 'American Express' : $card['brand'];
        $payeezy_value=$card['token'];
        $payeezy_cardholder_name=$card['name'];
        $payeezy_exp_date=$card['exp']['month'].substr($card['exp']['year'],-2);

        $addcard =  CardsInfo::create();
        $addcard->client_id=$card_tokenize->client_id;
        $addcard->payeezy_type = $payeezy_type;
        $addcard->payeezy_value = $payeezy_value;
        $addcard->payeezy_cardholder_name = $payeezy_cardholder_name;
        $addcard->payeezy_exp_date = $payeezy_exp_date;
        $addcard->save();

        $card_tokenize->clients_cardinfo_id = $addcard->id;
        $card_tokenize->status = 'success';
        $card_tokenize->description = 'Your Card has been added successfully.';
        $card_tokenize->save();
    }

    public function verifyTokenizeResponse(Request $request) {
        $client_token=$request->client_token;
        $card_tokenize = CardTokenize::where('client_token', $client_token)->first();
        if (count($card_tokenize)==0) {
            $data['status']='error';
            $data['description']='Connection Error: Card Tokenization Failed. Please refresh page and try again.';
            return response()->json($data);
        }
        if ($card_tokenize->status=='authorized') {
            $data['status']='error';
            $data['description']='Tokenization Error: Card Tokenization Failed. Please refresh page and try again.';
            return response()->json($data);
        }
        $data['status']=$card_tokenize->status;
        $data['description']=$card_tokenize->description;
        
        if ($card_tokenize->status=='success') {
            $client = Auth::user()->client;
            if (!$client->payeezy_value) {
                $card = CardsInfo::where('id', $card_tokenize->clients_cardinfo_id)->first();
                $client->payeezy_type = $card->payeezy_type;
                $client->payeezy_value = $card->payeezy_value;
                $client->payeezy_cardholder_name = $card->payeezy_cardholder_name;
                $client->payeezy_exp_date = $card->payeezy_exp_date;
                $client->save();
                $card->delete();
            }
        }
        return response()->json($data);
    }
    
}
