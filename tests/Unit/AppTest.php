<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class AppTest extends TestCase
{
    // public function test_createUserA()
    // {
    //     $response = $this->postJson(
    //         '/api/auth/register',
    //         [
    //             'username'=> 'adminsuper',
    //             'password'=> '12345',
    //         ],
    //     );

    //     $response
    //         ->assertHeader('content-type', 'application/json')
    //         ->assertStatus(Response::HTTP_CREATED)
    //         ->assertExactJson([
    //             'status' => true,
    //             'message' => 'Success',
    //         ]);
    // }

    public function test_createDuplicateUserA()
    {
        $response = $this->postJson(
            '/api/auth/register',
            [
                'username'=> 'adminsuper',
                'password'=> '12345',
            ],
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'status' => false,
                'message' => 'Username sudah ada!',
            ]);
    }

    public function test_loginUserA()
    {
        $response = $this->postJson(
            '/api/auth/login',
            [
                'username'=> 'adminsuper',
                'password'=> '12345',
            ],
        );

        $tokenArray = array($response->getData()->token, $response->getData()->token_type);

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'token',
                'token_type',
                'token_validity',
            ]);

        return $tokenArray;
    }

    public function test_invalidLoginUserA()
    {
        $response = $this->postJson(
            '/api/auth/login',
            [
                'username'=> 'adminsuper',
                'password'=> '55533535',
            ],
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertExactJson([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
    }

    /**
     * @depends test_loginUserA
     */
    public function test_refreshTokenA($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->postJson(
            '/api/auth/refresh'
        );

        $tokenArray = array($response->getData()->token, $response->getData()->token_type);

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'token',
                'token_type',
                'token_validity',
            ]);

        return $tokenArray;
    }

    public function test_invalidRefreshTokenA()
    {
        // load session
        $token = 'tokenxx';
        $tokenType = 'xxxxx';

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->postJson(
            '/api/auth/refresh'
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    // Below is kendaraan testing
    /**
     * @depends test_refreshTokenA
     */
    public function test_CheckStock($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->getJson(
            '/api/kendaraan/stok'
        );

        $firstIdMobil = $response->getData()->mobil[0]->_id;
        $firstIdMotor = $response->getData()->motor[0]->_id;

        $tokenArray = array($token, $tokenType, $firstIdMobil, $firstIdMotor);

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'mobil' => [
                    '*' => [
                        "_id",
                        "kendaraan" => [
                            "tahun_keluaran",
                            "warna",
                            "harga",
                        ],
                        "mesin",
                        "kapasitas_penumpang",
                        "tipe",
                        "stok",
                        "updated_at",
                        "created_at"
                    ]
                ],
                'motor' => [
                    '*' => [
                        "_id",
                        "kendaraan" => [
                            "tahun_keluaran",
                            "warna",
                            "harga",
                        ],
                        "mesin",
                        "tipe_suspensi",
                        "tipe_transmisi",
                        "stok",
                        "updated_at",
                        "created_at"
                    ]
                ]
            ]);

        return $tokenArray;
    }

    /**
     * @depends test_CheckStock
     */
    public function test_InvalidIdSellStock($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];
        $firstId = $tokenArray[2];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->postJson(
            '/api/kendaraan/penjualan',
            [
                '_id' => 'xxxxxx',
                'terjual' => 1000
            ]
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'status' => false,
                'message' => 'Id not found',
            ]);

        return $tokenArray;
    }

    /**
     * @depends test_CheckStock
     */
    public function test_InvalidSellStock($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];
        $firstId = $tokenArray[2];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->postJson(
            '/api/kendaraan/penjualan',
            [
                '_id' => $firstId,
                'terjual' => 1000
            ]
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'status' => false,
                'message' => 'Barang terjual melebihi stok',
            ]);

        return $tokenArray;
    }

    /**
     * @depends test_CheckStock
     */
    public function test_SellStockMobil($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];
        $firstId = $tokenArray[2];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->postJson(
            '/api/kendaraan/penjualan',
            [
                '_id' => $firstId,
                'terjual' => 3
            ]
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'status'  => true,
                'message' => 'Stok terjual!',
            ]);

        return $tokenArray;
    }

    /**
     * @depends test_CheckStock
     */
    public function test_SellStockMotor($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];
        $firstId = $tokenArray[3];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->postJson(
            '/api/kendaraan/penjualan',
            [
                '_id' => $firstId,
                'terjual' => 3
            ]
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'status'  => true,
                'message' => 'Stok terjual!',
            ]);

        return $tokenArray;
    }

    /**
     * @depends test_refreshTokenA
     */
    public function test_CheckSold($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->getJson(
            '/api/kendaraan/laporan'
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'mobil' => [
                    '*' => [
                        "_id",
                        "kendaraan" => [
                            "tahun_keluaran",
                            "warna",
                            "harga",
                        ],
                        "mesin",
                        "kapasitas_penumpang",
                        "tipe",
                        "terjual",
                        "updated_at",
                        "created_at"
                    ]
                ],
                'motor' => [
                    '*' => [
                        "_id",
                        "kendaraan" => [
                            "tahun_keluaran",
                            "warna",
                            "harga",
                        ],
                        "mesin",
                        "tipe_suspensi",
                        "tipe_transmisi",
                        "terjual",
                        "updated_at",
                        "created_at"
                    ]
                ]
            ]);

        return $tokenArray;
    }

    /**
     * @depends test_refreshTokenA
     */
    public function test_logout($tokenArray)
    {
        $token = $tokenArray[0];
        $tokenType = $tokenArray[1];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $tokenType . ' ' . $token
        ])->postJson(
            '/api/auth/logout'
        );

        $response
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'status' => true,
                'message' => 'Logged out!'
            ]);

        return $tokenArray;
    }
}
