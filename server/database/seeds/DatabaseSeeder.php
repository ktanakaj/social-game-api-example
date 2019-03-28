<?php

use Illuminate\Database\Seeder;
use App\Enums\AdminRole;
use App\Models\Admins\Administrator;

class DatabaseSeeder extends Seeder
{
    /**
     * DB初期データ生成。
     */
    public function run() : void
    {
        $admin = new Administrator();
        $admin->email = 'admin';
        $admin->password = bcrypt('admin01');
        $admin->role = AdminRole::ADMIN;
        $admin->note = '初期管理者';
        $admin->save();
    }
}
