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
        $mobil = Mobil::query()->get();
        $mobil = $mobil->makeHidden('terjual')->toArray();

        $motor = Motor::query()->get();
        $motor = $motor->makeHidden('terjual')->toArray();

        return response()->json(
            [
                'mobil' => $mobil,
                'motor' => $motor
            ]
        );
    }

    private function addSoldReport($table, $id, $banyak_penjualan) {
        $query = $table->first();
        // decrease with sell value
        // if stock < sell value, remove row id from stock
        if($query->stok < $banyak_penjualan) {
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
            $table->decrement('stok', $banyak_penjualan);
            $table->increment('terjual', $banyak_penjualan);
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
                'banyak_penjualan' => 'required|integer',
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
        $banyak_penjualan = $request->banyak_penjualan;

        // check if vehicle id in mobil
        $mobil = Mobil::where('_id', $id);
        $mobilQuery = $mobil->get();
        if($mobilQuery->count() > 0) {
            return $this->addSoldReport($mobil, $id, $banyak_penjualan);
        }

        // check if vehicle id in motor
        $motor = Motor::where('_id', $id);
        $motorQuery = $motor->get();
        if ($motorQuery->count() > 0) {
            return $this->addSoldReport($motor, $id, $banyak_penjualan);
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
        $mobil = Mobil::query()->get();
        $mobil = $mobil->makeHidden('stok')->toArray();

        $motor = Motor::query()->get();
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
