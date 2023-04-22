<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function($request,$next){
            $id=$request->route()->parameter('image');//URLの数字（ShopId）を取得　※文字列型
            if(!is_null($id)){
                $id=(int)$id;
                $imageId=Image::findOrFail($id)->owner->id;
                $ownerId=Auth::id();
                if($imageId!==$ownerId){//別のオーナーのショップページを開こうとした場合
                    abort(404);
                }
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $images=Image::where('owner_id',Auth::id())
            ->orderBy('updated_at','desc')
            ->paginate(20);
        return view('owner.images.index',compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
            return view('owner.images.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UploadImageRequest $request)
    {
        //
        $imageFiles=$request->file('files');//複数ファイルの場合
        if(!is_null($imageFiles)){
            foreach($imageFiles as $imageFile){
                $fileNameToStore=ImageService::upload($imageFile,'products');
                Image::create([
                    'owner_id'=>Auth::id(),
                    'filename'=>$fileNameToStore,
                ]);
            }
        }
        return redirect()->route('owner.images.index')
        ->with([
            'message'=>'画像登録を実施しました。',
            'status'=>'info',
        ]);
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
