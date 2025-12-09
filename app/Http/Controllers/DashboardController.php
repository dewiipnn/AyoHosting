<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Package;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->with('package')->orderBy('created_at', 'desc')->get();
        $tickets = Ticket::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        return view('dashboard', compact('user', 'orders', 'tickets'));
    }

    public function storeTicket(Request $request)
    {
        Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
        return response()->json(['success' => true]);
    }
}
