<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\SecondaryCategory;
use App\Models\Image;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'information',
        'price',
        'is_selling',
        'sort_order',
        'secondary_category_id',
        'image1',
        'image2',
        'image3',
        'image4',
    ];

    public function Shop(){
        return $this->belongsTo(Shop::class);//1対1のリレーション
    }
    
    ////////////////////特殊なリレーションの設定////////////////////////////
    public function category(){//!!メソッド名がsecondary_categoryではないので、Laravelが自動でIDを推測できない
        return $this->belongsTo(SecondaryCategory::class,'secondary_category_id');//第二引数で外部キーのカラム名を指定する
    }

    public function imageFirst(){//!!image1と、カラム名がまったく同じは設定できないので名前を変える
        return $this->belongsTo(Image::class,'image1','id');//第二引数が_idという形でないので、自動で推測されない⇒第三引数にで'id'に紐づくと明記
    }
    /**
     * tinkerで確認
     * $p=new App\Models\Product
     * $p->all()
     * $p->findOrFail(1)
     * $p->findOrFail(1)->shop
     * $p->findOrFail(1)->shop->owner
     * $p->findOrFail(1)->shop->owner->id
     * $p->findOrFail(1)->category
     * $p->findOrFail(1)->imageFirst
     * $p->findOrFail(1)->imageFirst->filename
     */
    ///////////////////////////////////////////////////////////////////////
 
    public function imageSecond(){//!!image1と、カラム名がまったく同じは設定できないので名前を変える
        return $this->belongsTo(Image::class,'image2','id');//第二引数が_idという形でないので、自動で推測されない⇒第三引数にで'id'に紐づくと明記
    }
    public function imageThird(){//!!image1と、カラム名がまったく同じは設定できないので名前を変える
        return $this->belongsTo(Image::class,'image3','id');//第二引数が_idという形でないので、自動で推測されない⇒第三引数にで'id'に紐づくと明記
    }
    public function imageFourth(){//!!image1と、カラム名がまったく同じは設定できないので名前を変える
        return $this->belongsTo(Image::class,'image4','id');//第二引数が_idという形でないので、自動で推測されない⇒第三引数にで'id'に紐づくと明記
    }

    public function stock(){
        return $this->hasMany(Stock::class);//1対多のリレーション
    }

    public function users(){//中間テーブル（ピボットテーブル）で多対多の関連付け
        return $this->belongsToMany(User::class,'carts')
        ->withPivot(['id','quantity']);
    }

    public function scopeAvailableItems($query){//ローカルスコープ
        $stocks=DB::table('t_stocks')
        ->select('product_id',DB::raw('sum(quantity) as quantity'))//rawで生のSQLを書く（sumを使うため）
        ->groupBy('product_id')//プロダクトごとに
        ->having('quantity','>=',1);//合計が１以上のもの

        return $query
            ->joinSub($stocks,'stock',function($join){//サブクエリ、サブクエリのテーブル名（stockという名前のテーブルを作る）、表示する列の情報
                $join->on('products.id','=','stock.product_id');//２つのテーブルの関連するID
            })
            ->join('shops','products.shop_id','=','shops.id')
            ->join('secondary_categories','products.secondary_category_id','=','secondary_categories.id')
            ->join('images as image1','products.image1','=','image1.id')
            ->where('shops.is_selling',true)
            ->where('products.is_selling',true)
            ->select('products.id as id','products.name as name','products.price','products.sort_order as sort_order',
            'products.information','secondary_categories.name as category','image1.filename as filename')
            ;
    }
    public function scopeSortOrder($query,$sortOrder){
        if($sortOrder===null || $sortOrder===\Constant::SORT_ORDER['recommend']){
            return $query->orderBy('sort_order','asc');
        }
        if($sortOrder===\Constant::SORT_ORDER['higherPrice']){
            return $query->orderBy('price','desc');
        }
        if($sortOrder===\Constant::SORT_ORDER['lowerPrice']){
            return $query->orderBy('price','asc');
        }
        if($sortOrder===\Constant::SORT_ORDER['later']){
            return $query->orderBy('products.created_at','desc');
        }
        if($sortOrder===\Constant::SORT_ORDER['older']){
            return $query->orderBy('products.created_at','asc');
        }
    }
    public function scopeSelectCategory($query,$categoryId){
        if($categoryId!=='0'){
            return $query->where('secondary_category_id',$categoryId);
        }
        else{
            return ;
        }
    }
    public function scopeSearchKeyword($query,$keyword){
        if(!is_null($keyword)){
            $spaceConvert=mb_convert_kana($keyword,'s');//全角スペースを半角に
            $keywords=preg_split('/[\s]+/',$spaceConvert,-1,PREG_SPLIT_NO_EMPTY);
            foreach($keywords as $word){
                $query->where('products.name','like','%'.$word.'%');
            }
            return $query;
        }
        else{
            return ;
        }
    }
}
