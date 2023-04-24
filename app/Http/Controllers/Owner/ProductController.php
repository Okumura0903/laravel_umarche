<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Product;
use App\Models\Shop;
use App\Models\PrimaryCategory;
use App\Models\Owner;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;//QueryBuilder  クエリビルダ
use Throwable;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
     {
         $this->middleware('auth:owners');
 
         $this->middleware(function($request,$next){
             $id=$request->route()->parameter('product');//URLの数字（productId）を取得　※文字列型
             if(!is_null($id)){
                 $id=(int)$id;
                 $productId=Product::findOrFail($id)->shop->owner->id;//リレーションでオーナーIDを取得
                 $ownerId=Auth::id();
                 if($productId!==$ownerId){//別のオーナーのプロダクトページを開こうとした場合
                     abort(404);
                 }
             }
             return $next($request);
         });
     }

    public function index()
    {
        //
        //$products=Owner::findOrFail(Auth::id())->shop->product;//オーナー所有のすべてのプロダクトを取得
        $ownerInfo=Owner::with('shop.product.imageFirst')//Eagerローディング（リレーションを含む書き方）N+1問題回避
        ->where('id',Auth::id())->get();
// //        dd($ownerInfo);
//         foreach($ownerInfo as $owner){
//             foreach($owner->shop->product as $product){
//                 dd($product->imageFirst->filename);
//             }
//         }
        return view('owner.products.index',compact('ownerInfo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $shops=Shop::where('owner_id',Auth::id())
        ->select('id','name')->get();
        $images=Image::where('owner_id',Auth::id())
        ->select('id','title','filename')
        ->orderBy('updated_at','desc')
        ->get();
        $categories=PrimaryCategory::with('secondary')//Eagerローディング
        ->get();
        return view('owner.products.create',compact('shops','images','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
//        dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'information' => ['required', 'string', 'max:1000'],
            'price'=>['required','integer'],
            'sort_order'=>['nullable','integer'],
            'quantity'=>['required','integer'],
            'shop_id'=>['required','exists:shops,id'],//shopsテーブルのIDに存在する
            'category'=>['required','exists:secondary_categories,id'],//secondary_categoriesテーブルのIDに存在する
            'image1'=>['nullable','exists:images,id'],//imagesテーブルのIDに存在する
            'image2'=>['nullable','exists:images,id'],
            'image3'=>['nullable','exists:images,id'],
            'image4'=>['nullable','exists:images,id'],
            'is_selling'=>['required']
        ]);
        
        try{
            DB::transaction(function() use($request){ //トランザクション
                $product=Product::create([
                    'name' => $request->name,
                    'information' => $request->information,
                    'price'=>$request->price,
                    'sort_order'=>$request->sort_order,
                    'shop_id'=>$request->shop_id,
                    'secondary_category_id'=>$request->category,
                    'image1'=>$request->image1,
                    'image2'=>$request->image2,
                    'image3'=>$request->image3,
                    'image4'=>$request->image4,
                    'is_selling'=>$request->is_selling
                ]);
                Stock::create([
                    'product_id'=>$product->id,
                    'type'=>1,
                    'quantity'=>$request->quantity,
                ]);
                
            },2);//うまくいかなかった場合２回再試行する
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }


        return redirect()
        ->route('owner.products.index')
        ->with(['message'=>'商品登録しました。','status'=>'info']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
