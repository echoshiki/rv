<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $userId = DB::table('users')->insertGetId([
            'name' => '微信用户7be3802bd6153cb2d4b859035d646237',
            'email' => 'quo.maxime@example.net',
            'phone' => '13218997189',
            'phone_verified_at' => now(),
            'password' => '$2y$12$J1pyQT3cpXeEbxcubyVReOa7iRdYtdL61LqJv9.ZK/QyQBQXeWMfe', 
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wechat_users')->insert([
            'user_id' => $userId,
            'openid' => 'oP0aB4uOgwqioYapbZSI_a42Yo74',
            'session_key' => 'eyJpdiI6IjlpbFpDOEFxSkI4MGtGZ2hKY3hsVVE9PSIsInZhbHVlIjoiRlkzeUxDOEZJT0RxQks2dzN0dU9FOXNlYmJ5SkNNanN0Q0IxUGpDY3BUdz0iLCJtYWMiOiI4NWU0N2U5NDJlMDUyMWE1ODY4NGJiM2U1M2Q0MDQ3ZjZlODY5YzlmMmI4YjkxY2ZkYTVhMjRiNWQ2ZTU3Y2U1IiwidGFnIjoiIn0',
            'raw_data' => null,
            'nickname' => null,
            'avatar_url' => null,
            'gender' => null,
            'country' => null,
            'province' => null,
            'city' => null,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //  
    }
};
