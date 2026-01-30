<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());
        $page = $request->query("page");
        $size = $request->query("size");
        if(!$page) $page = 1;
        if(!$size) $size = 12;
        $order = $request->query("order");
        if(!$order) $order = -1;
        $o_column = "";
        $o_order = "";
        switch($order){
            case 1:
                $o_column = "created_at";
                $o_order = "DESC";
                break;
            case 2:
                $o_column = "created_at";
                $o_order = "ASC";
                break;
            case 3:
                $o_column = "sale_price";
                $o_order = "ASC";
                break;
            case 4:
                $o_column = "sale_price";
                $o_order = "DESC";
                break;
            default:
                $o_column = "created_at";
                $o_order = "DESC";
        }
        $brands = Brand::orderBy('name','ASC')->get();
        $q_brands = $request->query("brands");
        $categories = Category::orderBy('name','ASC')->get();
        $q_categories = $request->query("categories");
        $prange = $request->query("prange");
        if(!$prange) $prange = "0,1000";
        $from  = explode(",",$prange)[0];
        $to  = explode(",",$prange)[1];

        $products = Product::where(function($query) use($q_brands){
            $query->whereIn('brand_id',explode(',',$q_brands))->orWhereRaw("'".$q_brands."'=''");
        })
        ->where(function($query) use($q_categories){
            $query->whereIn('category_id',explode(',',$q_categories))->orWhereRaw("'".$q_categories."'=''");
        })
        ->whereBetween('regular_price',array($from,$to))
        ->where('name','like','%'.$request->query("query").'%')
        ->where('name','like','%'.$request->query("search-keyword").'%')
        ->orderBy($o_column,$o_order)->paginate($size);
        return view('shop',compact('products','page','size','order','brands','q_brands','categories','q_categories','from','to'));
    }
    public function product_details($product_slug)
{
    $product = Product::where('slug',$product_slug)->first();
    $rproducts = Product::where('slug','<>',$product_slug)->get()->take(8);
    return view('details',compact('product','rproducts'));
}
}
