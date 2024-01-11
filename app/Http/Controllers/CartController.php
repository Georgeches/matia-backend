<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(){
        $cart = session()->get('cart', []);
        $total = 0;
        $tax = 0;
        foreach($cart as $item){
            $total+=$item['price']*$item['amount'];
            $tax+=0.16*$total;
        }
        return view('cart.index', [
            'total'=>$total,
            'tax'=>$tax,
        ]);
    }

    public function addToCart($id){
        $product = Product::find($id);
        $cart = session()->get('cart', []);

        if($product == null){
            return redirect()->back()->with('error', 'Error fetching product. Please try again later');
        }

        if(isset($cart[$id])){
            return redirect()->back()->with('success', 'Product already in cart');
        }
        else{
            $product['amount'] = 1;
            $cart[$id] = $product;
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart');
    }

    public function removeFromCart($id){
        $cart = session()->get('cart', []);

        if(isset($cart[$id])){
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product removed from cart');
        }
    }

    public function increaseQuantity($id){
        $cart = session()->get('cart', []);

        if($cart){
            if($cart[$id]['amount']<$cart[$id]['quantity']){
                $cart[$id]['amount'] += 1;
                return redirect()->back();
            }
            else{
                return redirect()->back()->with('error', 'Quantity remaining is '.$cart[$id]['quantity']);
            }
        }
        else{
            return redirect()->back()->with('error', 'Could not update cart. Please try again later');
        }
    }

    public function decreaseQuantity($id){
        $cart = session()->get('cart', []);

        if($cart){
            if($cart[$id]['amount'] >= 2){
                $cart[$id]['amount'] -= 1;
                return redirect()->back();
            }
            else{
                return redirect()->back()->with('error', 'Quantity cannot be lower than 1');
            }
        }
        else{
            return redirect()->back()->with('error', 'Could not update cart. Please try again later');
        }
    }
}
