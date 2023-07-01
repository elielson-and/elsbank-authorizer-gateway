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
            'value' => 'required'
        ]);

      

        if ($validator->fails()) {
            return response()->json(['message' => 'failed to process, missing some parameter(s).'], 400);
        }
        
        //TODO: join to one consult
        $payer_balance = User::with('wallet')->where('uuid', $request->payer)->first()->wallet->balance;
        $payee_balance = User::with('wallet')->where('uuid', $request->payee)->first()->wallet->balance;
        return response()->json([
            'payer_balance' => $payer_balance,
            'payee_balance' => $payee_balance
        ],200);

        // $transaction = Transaction::create([
        //     'uuid' => Str::uuid(),
        //     'payer' => $request->payer,
        //     'payee' => $request->payee,
        //     'value' => $request->value
        // ]);
    }
}
