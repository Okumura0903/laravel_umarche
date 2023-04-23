<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\SecondaryCategory;
use App\Models\Image;
use App\Models\Stock;

class Product extends Model
{
    use HasFactory;

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
 
    public function stock(){
        return $this->hasmany(Stock::class);//1対多のリレーション
    }
}
