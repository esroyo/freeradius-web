<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use FreeradiusWeb\Http\Controllers\API\RadacctController;
use FreeradiusWeb\User;

class RadacctControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the session granularization.
     *
     * @return void
     */
    public function testGranularize()
    {
        // seed
        $this->seed();
        DB::table('radacct')->insert([[
            'acctuniqueid' => str_random(32),
            'acctstarttime' => '2016-05-20 19:47:51',
            'acctstoptime' => '2016-05-21 00:31:12',
            'acctsessiontime' =>  17001,
            'username' => 'tester'
        ], [
            'acctuniqueid' => str_random(32),
            'acctstarttime' => '2016-05-21 00:31:13',
            'acctstoptime' => '2016-05-21 00:31:18',
            'acctsessiontime' =>  5,
            'username' => 'tester'
        ], [
            'acctuniqueid' => str_random(32),
            'acctstarttime' => '2016-05-21 00:31:19',
            'acctstoptime' => '2016-05-21 00:31:19', // test with no duration
            'acctsessiontime' =>  1,
            'username' => 'tester'
        ]]);

        $expected = [
            [
                'starttime' => '2016-05-20 00:00:00',
                'stoptime' => '2016-05-20 23:59:59',
                'sessiontime' =>  15128
            ],
            [
                'starttime' => '2016-05-21 00:00:00',
                'stoptime' => '2016-05-21 23:59:59',
                'sessiontime' =>  1877 // 1872 + 5
            ]
        ];

        $user = User::where('name', '=', 'admin')->first();
        $token = JWTAuth::fromUser($user);

        $this->get('/api/v1/radacct?' . http_build_query([
            'start_date' => '20160520',
            'end_date' => '20160522',
            'timezone' => 'UTC',
            'metrics' => 'sessiontime',
            'granularity' => 'day'
        ]), ['HTTP_Authorization' => "Bearer $token"])
            ->seeJson($expected);
    }
}
