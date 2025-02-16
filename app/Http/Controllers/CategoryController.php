<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //add Category
    public function addCategory(){
        try{

        }catch(Exception $e){
            return response()->json([
                'message'=> $e->getMessage(),
            ],500);
        }
    }
}
