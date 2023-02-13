<?php

namespace Database\Seeders;

use App\Models\Column;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('columns')->delete();
		$columns = array(
		array('tooltip' => "Id", 'category' => "all", 'value'=>"id", 'page_name'=> "socialmedia"),
		array('tooltip' => "Name", 'category' => "all", 'value'=>"name", 'page_name'=> "socialmedia"),
		array('tooltip' => "Image", 'category' => "all", 'value'=>"image", 'page_name'=> "socialmedia"),
		array('tooltip' => "Website", 'category' => "all", 'value'=>"website", 'page_name'=> "socialmedia"),
		array('tooltip' => "Status", 'category' => "all", 'value'=>"status", 'page_name'=> "socialmedia"),
	);
	DB::table('columns')->insert($columns);

    }
}
