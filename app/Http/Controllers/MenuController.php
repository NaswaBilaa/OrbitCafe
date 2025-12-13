<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function all($category = null)
    {
        $categories = Category::all();

        if ($category) {
            $catModel = Category::where('name', $category)->firstOrFail();
            $menus = Menu::where('category_id', $catModel->id)->get();
        } else {
            $menus = Menu::all();
        }

        return view('all', compact('menus', 'categories', 'category'));
    }

    public function showOrderDetail($id)
    {
        return view('detailsmenu.orderdetails', [
            'menu' => Menu::findOrFail($id),
            'toppings' => Topping::all()->groupBy('type'),
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'size_id'  => 'nullable|exists:toppings,id',
            'sugar_id' => 'nullable|exists:toppings,id',
            'ice_id'   => 'nullable|exists:toppings,id',
            'mode'     => 'nullable|in:cart,buy_now', 
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);

        $toppingIds = array_filter([
            $validated['size_id']  ?? null,
            $validated['sugar_id'] ?? null,
            $validated['ice_id']   ?? null,
        ]);

        $toppings = Topping::whereIn('id', $toppingIds)->get();

        $additionalPrice = $toppings->sum('price');
        $itemPrice = $menu->price + $additionalPrice;

        $cart = Session::get('cart', []);
        $newToppingIdsSorted = $toppings->pluck('id')->sort()->values()->all();
        $existingKey = null;

        foreach ($cart as $key => $item) {
            if ($item['menu_id'] != $menu->id) {
                continue;
            }

            $existingToppingIds = collect($item['toppings'])
                ->pluck('id')
                ->sort()
                ->values()
                ->all();

            if ($existingToppingIds === $newToppingIdsSorted) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            $cart[$existingKey]['quantity'] += $validated['quantity'];
            $cart[$existingKey]['subtotal']  = $cart[$existingKey]['item_price'] * $cart[$existingKey]['quantity'];
        } else {
            $uuid = Str::uuid()->toString();

            $cart[$uuid] = [
                'uuid'        => $uuid,
                'menu_id'    => $menu->id,
                'menu_name'  => $menu->name,
                'menu_image' => $menu->image,
                'base_price'  => $menu->price,
                'quantity'    => $validated['quantity'],
                'toppings'    => $toppings->map(fn ($t) => $t->only(['id', 'name', 'price', 'type']))->toArray(),
                'item_price'  => $itemPrice,
                'subtotal'    => $itemPrice * $validated['quantity'],
            ];
        }

        Session::put('cart', $cart);

        $mode = $validated['mode'] ?? 'cart';

        if ($mode === 'buy_now') {
            Session::put('buy_now', true);
            return redirect()->route('checkout.show');
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Minuman berhasil ditambahkan ke keranjang!');
    }

    public function showCart()
    {
        $cartItems = Session::get('cart', []);
        $totalPrice = $this->calculateCartTotal($cartItems);

        return view('detailsmenu.orderbills', compact('cartItems', 'totalPrice'));
    }

    public function updateCartItem(Request $request)
    {
        $validated = $request->validate([
            'uuid' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = Session::get('cart', []);
        $uuid = $validated['uuid'];

        if (!isset($cart[$uuid])) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
        }

        if ($validated['quantity'] > 0) {
            $cart[$uuid]['quantity'] = $validated['quantity'];
            $cart[$uuid]['subtotal'] = $cart[$uuid]['item_price'] * $validated['quantity'];
            Session::put('cart', $cart);
        } else {
            return $this->removeCartItem($uuid);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas diperbarui.',
            'item' => $cart[$uuid],
            'total_price' => $this->calculateCartTotal($cart),
        ]);
    }

    public function removeCartItem(Request $request, $uuid)
    {
        $cart = Session::get('cart', []);

        if (!isset($cart[$uuid])) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
            }
            return redirect()->back()->with('error', 'Item tidak ditemukan.');
        }

        unset($cart[$uuid]);
        Session::put('cart', $cart);

        $response = [
            'success'     => true,
            'message'     => 'Item berhasil dihapus.',
            'total_price' => $this->calculateCartTotal($cart),
        ];

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        return redirect()->back()->with('success', 'Item berhasil dihapus.');
    }


    private function calculateCartTotal(array $cart): float
    {
        return collect($cart)->sum('subtotal');
    }

    public function clearCart()
    {
        Session::forget('cart');
        Session::forget('buy_now'); 
        return redirect()
            ->route('cart.index')
            ->with('success', 'Keranjang berhasil dikosongkan.');
    }
    
}
