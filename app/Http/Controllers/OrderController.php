<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'package_id' => $request->package_id,
                'domain' => $request->domain,
                'total' => $request->price,
                'payment_method' => 'WhatsApp', 
                'status' => 'pending'
            ]);
            
            return response()->json(['success' => true, 'order_id' => $order->id]);
        } catch (\Exception $e) {
            // KIRIM ERROR ASLI KE FRONTEND
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($order->status == 'active') {
            return back()->with('error', 'Pesanan yang sudah aktif tidak dapat dibatalkan!');
        }

        $order->delete();

        // Cek dari tab mana user klik tombol ini
        $tab = $request->input('source_tab', 'services'); // Default ke services

        return redirect('/dashboard?tab=' . $tab)->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
