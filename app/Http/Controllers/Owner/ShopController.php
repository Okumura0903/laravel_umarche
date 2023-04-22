<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use Illuminate\Support\Facades\Storage;
use InterventionImage;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;

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
        $shop=Shop::findOrFail($id);
        return view('owner.shops.edit',compact('shop'));
    }

    public function update(UploadImageRequest $request, string $id)//画像アップロード
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'information' => ['required', 'string', 'max:1000'],
            'is_selling' => ['required'],
        ]);

        $imageFile=$request->image;
        if(!is_null($imageFile) && $imageFile->isValid()){
            $fileNameToStore=ImageService::upload($imageFile,'shops');
        }

        $shop=Shop::findOrFail($id);
        $shop->name=$request->name;
        $shop->information=$request->information;
        $shop->is_selling=$request->is_selling;
        if(!is_null($imageFile) && $imageFile->isValid()){
            $shop->filename=$fileNameToStore;
        }
        $shop->save();
        return redirect()->route('owner.shops.index')
        ->with([
            'message'=>'ショップ情報を更新しました。',
            'status'=>'info'
        ]);
    }
}
