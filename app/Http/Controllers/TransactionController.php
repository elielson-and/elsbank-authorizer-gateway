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
        $payer_data = User::with('wallet')->where('uuid', $request->payer)->first();
        $payee_data = User::with('wallet')->where('uuid', $request->payee)->first();
       
        if ($request->value > $payer_data->wallet->balance){
            return response()->json(['message'=>"Unable to process. Payer doesn't have sufficient funds"],401);
        }

        try {

            // Subtracting from the payer
            $payer = User::where('uuid',$request->payer)->first();
            $payer->wallet()->update([
                'balance' => floatval($payer_data->wallet->balance) - floatval($request->value)
            ]);

            // Crediting the beneficiary
            $payee = User::where('uuid',$request->payee)->first();
            $payee->wallet()->update([
                'balance' => floatval($payee_data->wallet->balance) + floatval($request->value)
            ]);

            // Register transaction info
            $transaction = Transaction::create([
                'uuid' => Str::uuid(),
                'payer' => $request->payer,
                'payee' => $request->payee,
                'value' => $request->value
            ]);

            return response()->json([
                ['message' => 'success'],
                [
                    'transaction' => [
                        'id' => $transaction->uuid,
                        'payer_name' => strtoupper($payer_data->first_name.' '.$payer_data->last_name),
                        'payee_name' => strtoupper($payee_data->first_name.' '.$payee_data->last_name),
                        'value' => $request->value,
                        'finished_at' => $transaction->created_at
                    ]
                ]
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error has ocurred, please try again later.',
                'error_details' => $th
            ], 500);
        }
        

    }
}
