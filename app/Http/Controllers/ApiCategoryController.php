<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiCategoryController extends Controller
{
    public function  list_category(){
        try {
            $list_category=DB::table('tbl_category')->get();
            $result=$this->tree_category($list_category);
            return response()->json(['data'=>$result]);
        } catch (\Throwable $th) {
            return response()->json(['error'=>'error']);
        }
    }

    public function tree_category( $list_category,$category_parent_id=0){
        $result=[];
        foreach ($list_category as $categories => $category) {
            if($category->category_parent_id==$category_parent_id){
                unset($list_category[$categories]);
                $child=$this->tree_category($list_category,$category->category_id);
                $category->child[]=$child;
                $result[]=$category;
            }
        }
        return $result;
    }

    public function category_column($id){
        $list_category=DB::table('tbl_category')->where('category_id',$id)->first();

        $category_dequy_up=$this->dequy_up($list_category->category_id);
        $result=$this->tree_category_column($category_dequy_up->category_id);
        dd($result);
    }
    public function tree_category_column( $category_id){
        $check=DB::table('tbl_category')->where('category_parent_id',$category_id)->get()->toArray();
        $result=[];
        if(!empty($check)){
            foreach ($check as $categories => $category) {
                $result[]=$category;
                $result=array_merge($result,$this->tree_category_column($category->category_id));
               
            }
        }
        return $result;
    }
    public function dequy_up($category_parent){
        $check=DB::table('tbl_category')->where('category_id',$category_parent)->first();
        if(!empty($check) && $check->category_parent_id!==0){
            $check=$this->dequy_up($check->category_parent_id);
        }
        return $check;
    }
}
