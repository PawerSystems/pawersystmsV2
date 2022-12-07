<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = array();
        $file = fopen(storage_path("permissions.txt"), "r");

        while(!feof($file)) {
            array_push($permissions,fgets($file));
        }
        fclose($file);
        
        //---- Add permissions to Super Admin ----//
        foreach($permissions as $title){
            if($title){
                $permission = Permission::create([
                    'title' => $title,
                    'business_id' => 1,
                ]);
                //------- Assign Permissions to this role ----//
                DB::table('permission_role')->insert([
                    'role_id' => 1,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
}
