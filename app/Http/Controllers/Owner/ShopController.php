<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;

class ShopController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function($request,$next){
            $id=$request->route()->parameter('shop');//URLの数字（ShopId）を取得　※文字列型
            if(!is_null($id)){
                $id=(int)$id;
                $shopId=Shop::findOrFail($id)->owner->id;
                $ownerId=Auth::id();
                if($shopId!==$ownerId){//別のオーナーのショップページを開こうとした場合
                    abort(404);
                }
            }
            return $next($request);
        });
    }
    public function index()
    {
        $ownerId=Auth::id();
        $shops=Shop::where('owner_id',$ownerId)->get();
        return view('owner.shops.index',compact('shops'));
    }
    public function edit(string $id)
    {
        $ownerId=Auth::id();
        Shop::findOrFail($id);

    }
    public function update(Request $request, string $id)
    {

    }
}
