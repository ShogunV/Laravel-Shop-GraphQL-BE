<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cart = $request->input('cart');
        $total = $request->input('total');
        $totalQuantity = $request->input('totalQuantity');
        $user = Auth::user();
        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));

        Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'total_quantity' => $totalQuantity,
            'data' => (string) collect($cart)
        ]);

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'price_data' => [
                'currency' => 'eur',
                'unit_amount' => (int) $total * 100,
                'product_data' => [
                  'name' => 'Your cart total',
                  'images' => ["https://i.imgur.com/EHyR2nP.png"],
                ],
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => env('FRONT_END_URL') . '/cart?success=true',
            'cancel_url' => env('FRONT_END_URL') . '/cart?canceled=true',
          ]);

          return response(['url' => $checkout_session->url], Response::HTTP_OK);
    }

}
