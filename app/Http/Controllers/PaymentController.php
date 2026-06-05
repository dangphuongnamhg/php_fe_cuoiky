<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function qr(Request $request)
    {
        return view('payments.qr');
    }

    public function result(Request $request)
    {
        $status = $request->get('status', 'success');
        return view('payments.result', compact('status'));
    }
}
