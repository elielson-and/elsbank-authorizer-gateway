<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function transfer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'payer' => 'required',
            'payee' => 'required',
            'value' => 'required|gte:0.01'
        ]);

      

        if ($validator->fails()) {
            return response()->json(['message' => 'failed to process, missing or invalid parameter(s).'], 400);
        }
        
        //TODO: join to one consult
        $payer_balance = floatVal(User::with('wallet')->where('uuid', $request->payer)->first()->wallet->balance);
        $payee_balance = floatVal(User::with('wallet')->where('uuid', $request->payee)->first()->wallet->balance);
       
        if ($request->value > $payer_balance){
            return response()->json(['message'=>"Can't process this transaction. Payer has no suficient founds"],401);
        }


    }
}
