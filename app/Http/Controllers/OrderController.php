<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public static function generateInvoiceNumber()
    {
        $timestamp = now()->format('YmdHis');
        $rand = mt_rand(100, 999); 
        return 'INV-' . $timestamp . '-' . $rand;
    }

    public function showPaymentPage(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $totalItems = $order->items()->sum('quantity');
        $payment    = $order->payment;

        return view('payment.payments', compact('order', 'totalItems', 'payment'));
    }

    public function checkout(Request $request)
    {
        $cart     = session('cart', []);
        $selected = $request->input('selected', []);

        if (!empty($selected)) {
            $cartItems = array_intersect_key($cart, array_flip($selected));
        } else {
            $cartItems = $cart;
        }

        $totalPrice = array_sum(array_column($cartItems, 'subtotal'));

        $tables = Table::all();

        return view('detailsmenu.checkout', compact('cartItems', 'totalPrice', 'tables', 'selected'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'        => 'required|string|max:255',
            'no_telepon'          => 'required|string|max:20',
            'table_id'            => 'required|exists:tables,id',
            'items'               => 'required|array',
            'items.*.drink_id'    => 'required|exists:drinks,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.toppings'    => 'nullable|array',
            'items.*.toppings.*'  => 'exists:toppings,id',
        ]);

        $selectedUuids = $request->input('selected', array_keys($validated['items'] ?? []));

        if (!empty($selectedUuids)) {
            $validated['items'] = array_intersect_key(
                $validated['items'],
                array_flip($selectedUuids)
            );
        }

        if (empty($validated['items'])) {
            return back()
                ->withErrors(['error' => 'Tidak ada item yang dipilih untuk checkout.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $invoice    = self::generateInvoiceNumber();
            $totalPrice = 0;

            $order = Order::create([
                'user_id'       => auth()->id(),
                'invoice_number'=> $invoice,
                'total_price'   => 0,
                'status'        => 'pending',
                'nama_lengkap'  => $validated['nama_lengkap'],
                'no_telepon'    => $validated['no_telepon'],
                'table_id'      => $validated['table_id'],
            ]);

            foreach ($validated['items'] as $item) {
                $drink       = \App\Models\Drink::findOrFail($item['drink_id']);
                $drinkPrice  = $drink->price;
                $toppingTotal= 0;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'drink_id' => $drink->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => 0,
                ]);

                if (!empty($item['toppings'])) {
                    foreach ($item['toppings'] as $toppingId) {
                        $topping       = \App\Models\Topping::findOrFail($toppingId);
                        $toppingTotal += $topping->price;

                        OrderItemTopping::create([
                            'order_item_id' => $orderItem->id,
                            'topping_id'    => $topping->id,
                        ]);
                    }
                }

                $subtotal = ($drinkPrice + $toppingTotal) * $item['quantity'];
                $orderItem->update(['subtotal' => $subtotal]);
                $totalPrice += $subtotal;
            }

            $order->update(['total_price' => $totalPrice]);

            $snapToken = PaymentController::createOrRefreshSnapToken($order);

            DB::commit();

            $cart = session('cart', []);
            foreach ($selectedUuids as $uuid) {
                unset($cart[$uuid]);
            }
            session(['cart' => $cart]);
            session()->forget('buy_now');

            // redirect ke halaman pembayaran
            return redirect()->route('payment.page', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to place order: ' . $e->getMessage()]);
        }
    }
}
