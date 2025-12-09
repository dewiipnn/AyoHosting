<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Order;

class AdminController extends Controller
{
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            return redirect('/dashboard');
        }
        $packages = Package::all();
        $orders = Order::with(['user', 'package'])->orderBy('created_at', 'desc')->get();
        return view('admin', compact('packages', 'orders'));
    }

    public function storePackage(Request $request)
    {
        Package::create($request->all());
        return response()->json(['success' => true]);
    }

    public function updatePackage(Request $request, $id)
    {
        Package::find($id)->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroyPackage($id)
    {
        Package::destroy($id);
        return response()->json(['success' => true]);
    }
}
