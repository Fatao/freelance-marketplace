<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'complainable_id'   => 'required|integer',
            'complainable_type' => 'required|in:order,user',
            'reason'            => 'required|string|min:10|max:1000',
        ]);

        $type = $data['complainable_type'] === 'order'
            ? Order::class
            : User::class;

        Complaint::create([
            'author_id'         => Auth::id(),
            'complainable_id'   => $data['complainable_id'],
            'complainable_type' => $type,
            'reason'            => $data['reason'],
            'status'            => 'pending',
        ]);

        return back()->with('success', 'Жалоба отправлена на рассмотрение.');
    }
}