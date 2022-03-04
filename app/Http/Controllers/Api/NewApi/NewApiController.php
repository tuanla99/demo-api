<?php

namespace App\Http\Controllers\Api\NewApi;

use App\Http\Controllers\Controller;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewApiController extends Controller
{
    public function index(Request $request)
    {
       $data = $request->all();
       $table = $data['table'];
       unset($data['table']);
       if ($table && $data['id']) {
         try {
          if (Schema::hasTable($table)) {
            echo 'true';
            $this->returnResponseWhenInsert($this->updateTable($table, $data)) ;
          } else {
            echo 'false';
            $this->returnResponseWhenInsert($this->createNewTable($table, $data));
          }
         } catch (\Exception $e) {
          return response()->json([
            'error' => '500',
            'message' => $e->getMessage(),
          ], 500);
         }
       }
    }

    public function createNewTable($nameTable, $data)
    {
      Schema::create($nameTable, function (Blueprint $table)  use ($data) {
        foreach ($data as $key => $val) {
          $table->string($key)->nullable();
        }
        $table->timestamps();
    });
      $now = (new DateTime())->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'))
                ->format('Y-m-d H:i');
      $data['created_at'] = $now;
      $data['updated_at'] = $now;
      return DB::table($nameTable)->insert($data); // true|false
    }

    public function updateTable($table, $data)
    {
      $columns = array_diff(Schema::getColumnListing($table), ['created_at', 'updated_at']);
      $columnsInRequest =  array_keys($data);
      $newColumns = array_diff($columnsInRequest, $columns);
      $newColumnsWithData = [];

      foreach ($newColumns as $column) {
         Schema::table($table, function (Blueprint $blueprint) use ($column) {
           $blueprint->text($column)->nullable();
         });
         $newColumnsWithData[$column] = $data[$column];
      }

      $now = (new DateTime())->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'))
              ->format('Y-m-d H:i');
      $data['updated_at'] = $now;
      $record = DB::table($table)->where('id', '=',$data['id'])->first();
      if ($record) {
        return  DB::table($table)->where('id', $data['id'])->update($data);
      } else {
        $data['created_at'] = $now;
        return  DB::table($table)->insert($data);
      }
    }

    public function returnResponseWhenInsert($isInsert)
    {
      if ($isInsert) {
        return response()->json([
          'error' => '',
          'message' => 'Insert successfully'
        ], 201);
      } else {
         return response()->json([
           'error' => '500',
           'message' => 'Something went wrong',
         ], 500);
      }
    }
}
