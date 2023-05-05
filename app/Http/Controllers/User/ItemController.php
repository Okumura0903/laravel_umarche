<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\mail;
use App\Models\PrimaryCategory;
use App\Mail\TestMail;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');

        $this->middleware(function($request,$next){
            $id=$request->route()->parameter('item');//URLの数字（productId）を取得　※文字列型
            if(!is_null($id)){
                $id=(int)$id;
                $itemId=Product::AvailableItems()->where('products.id',$id)->exists();//パラメータで入ってきたものが表示できる商品か？
                if(!$itemId){//exists()なのでtrueかfalse 存在していなかったら
                    abort(404);
                }
            }
            return $next($request);
        });
    }
    //
    public function index(Request $request){
        Mail::to('test@example.com')
        ->send(new TestMail());

        $categories=PrimaryCategory::with('secondary')//Eagerローディング
        ->get();

        $products=Product::AvailableItems()
        ->selectCategory($request->category ?? '0')
        ->searchKeyword($request->keyword)
        ->sortOrder($request->sort)
        ->paginate($request->pagination ?? '20');//ローカルスコープ

        // $stocks=DB::table('t_stocks')
        // ->select('product_id',DB::raw('sum(quantity) as quantity'))//rawで生のSQLを書く（sumを使うため）
        // ->groupBy('product_id')//プロダクトごとに
        // ->having('quantity','>=',1);//合計が１以上のもの

        // //JOIN
        // $products=DB::table('products')
        //     ->joinSub($stocks,'stock',function($join){//サブクエリ、サブクエリのテーブル名（stockという名前のテーブルを作る）、表示する列の情報
        //         $join->on('products.id','=','stock.product_id');//２つのテーブルの関連するID
        //     })
        //     ->join('shops','products.shop_id','=','shops.id')
        //     ->join('secondary_categories','products.secondary_category_id','=','secondary_categories.id')
        //     ->join('images as image1','products.image1','=','image1.id')
        //     ->join('images as image2','products.image2','=','image2.id')
        //     ->join('images as image3','products.image3','=','image3.id')
        //     ->join('images as image4','products.image4','=','image4.id')
        //     ->where('shops.is_selling',true)
        //     ->where('products.is_selling',true)
        //     ->select('products.id as id','products.name as name','products.price','products.sort_order as sort_order',
        //     'products.information','secondary_categories.name as category','image1.filename as filename')
        //     ->get();
         //dd($stocks,$products);
        // $products=Product::all();

        return view('user.index',compact('products','categories'));
    }
    public function show($id){
        $product=Product::findOrFail($id);
        $quantity=Stock::where('product_id',$product->id)->sum('quantity');
        if($quantity>9){
            $quantity=9; 
        }

        return view('user.show',compact('product','quantity'));
    }
}
