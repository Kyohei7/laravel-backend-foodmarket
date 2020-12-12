<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // API Get Data Transaction All
    public function all(Request $request)
    {
        // Create Parameter Transaction Filter
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $food_id = $request->input('food_id');
        $status = $request->input('status');

        if ($id) {

            // Relation Transaction to user & food
            $transaction = Transaction::with(['food', 'user'])->find($id);

            if ($transaction) {
                return ResponseFormatter::success(
                    $transaction,
                    'Success Get Data Transaction By ID'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Transaction Not Found',
                    404
                );
            }
        }

        // Get Data Transaction by User was Login
        $transaction = Transaction::with(['food', 'user'])
            ->where('user_id', Auth::user()->id);

        // Get Data Transaction By ID Food
        if ($food_id) {
            $transaction->where('food_id', $food_id);
        }

        // Get Data Transaction By Status
        if ($status) {
            $transaction->where('status', $status);
        }

        // Response Data
        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Success Get Data List Transaction'
        );
    }

    // API Update Data Transaction by ID
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->update($request->all());

        return ResponseFormatter::success($transaction, 'Success Update Transaction');
    }
}
