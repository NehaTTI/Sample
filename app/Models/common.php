<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
class Common extends Model
{
    
    public static function add_details($table,$data){
        DB::table($table)->insert($data);
        return true;
    } 
    public static function getDetails($table){
        return DB::table($table)->get();
    }
    
     public static function update_details($table,$data,$where){
        DB::table($table)->where($where)->update($data);
        return true;
    } 
}