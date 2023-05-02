<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\User;

class CartController extends Controller
{
    //
    public function index(){
        $user=User::findOrFail(Auth::id());
        $products=$user->products;
        $totalPrice=0;
        foreach($products as $product){
            $totalPrice+=$product->price*$product->pivot->quantity;
        }
//        dd($products,$totalPrice);

        return view('user.cart',compact('products','totalPrice'));
    }
    public function add(Request $request){
//        dd($request);
        //cartに同じ商品が入っているか？
        $itemInCart=Cart::where('product_id',$request->product_id)
        ->where('user_id',Auth::id())->first();
        if($itemInCart){
            $itemInCart->quantity+=$request->quantity;
            $itemInCart->save();
        }
        else{
            Cart::create([
                'user_id'=>Auth::id(),
                'product_id'=>$request->product_id,
                'quantity'=>$request->quantity
            ]);
        }
        return redirect()->route('user.cart.index');
    }
    public function delete($id){
        Cart::where('product_id',$id)
        ->where('user_id',Auth::id())
        ->delete();
        return redirect()->route('user.cart.index');
    }
}