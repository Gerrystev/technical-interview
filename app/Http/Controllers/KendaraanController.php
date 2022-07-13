<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


class KendaraanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = $this->guard()->user();

    }

    public function checkStock() 
    {
        $mobil = Mobil::where('stok', '>', 0)->get();
        $mobil = $mobil->makeHidden('terjual')->toArray();

        $motor = Motor::where('stok', '>', 0)->get();
        $motor = $motor->makeHidden('terjual')->toArray();

        return response()->json(
            [
                'mobil' => $mobil,
                'motor' => $motor
            ]
        );
    }

    private function addSoldReport($table, $id, $terjual) {
        $query = $table->first();
        // decrease with sell value
        // if stock < sell value, remove row id from stock
        if($query->stok < $terjual) {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Barang terjual melebihi stok',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        else {
            // update sold stock
            $table->decrement('stok', $terjual);
            $table->increment('terjual', $terjual);
        }

        return response()->json(
            [
                'status'  => true,
                'message' => 'Stok terjual!',
            ]
        );
    }

    public function sellStock(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                '_id' => 'required|string',
                'terjual' => 'required|integer',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $id = $request->_id;
        $terjual = $request->terjual;

        // check if vehicle id in mobil
        $mobil = Mobil::where('_id', $id);
        $mobilQuery = $mobil->get();
        if($mobilQuery->count() > 0) {
            return $this->addSoldReport($mobil, $id, $terjual);
        }

        // check if vehicle id in motor
        $motor = Motor::where('_id', $id);
        $motorQuery = $motor->get();
        if ($motorQuery->count() > 0) {
            return $this->addSoldReport($motor, $id, $terjual);
        }

        // return error
        return response()->json(
            [
                'status' => false,
                'message' => 'Id not found',
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    public function soldReport()
    {
        $mobil = Mobil::where('terjual', '>', 0)->get();
        $mobil = $mobil->makeHidden('stok')->toArray();

        $motor = Motor::where('terjual', '>', 0)->get();
        $motor = $motor->makeHidden('stok')->toArray();

        return response()->json(
            [
                'mobil' => $mobil,
                'motor' => $motor
            ]
        );
    }

    protected function guard()
    {
        return Auth::guard();

    }
}
