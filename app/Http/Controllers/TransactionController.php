<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        $rules = Validator::make([
            'payer', 'payee', 'value'
        ]);

        if($rules->fails()){
            return response()->json(['message'=>'failed to process, missing some parameter.'],401);
        }

        $transaction = Transaction::create([
            'uuid' => Str::uuid(),
            'payer' => $request->payer,
            'payee' => $request->payee,
            'value' => $request->value
        ]);


    }

    
}
