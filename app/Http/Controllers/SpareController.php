<?php
namespace App\Http\Controllers;
use App\Models\{SpareItem,SpareMovement};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpareController extends Controller
{
    public function issue(Request $request, SpareItem $spare)
    {
        if (!auth()->user()->isTechnician() && !auth()->user()->isChiefEngineer()) abort(403);
        $request->validate(['quantity'=>'required|integer|min:1','work_order_id'=>'nullable|exists:work_orders,id']);
        if ($spare->quantity < $request->quantity) return back()->withErrors('Insufficient stock.');
        DB::transaction(function () use ($request,$spare) {
            $spare->decrement('quantity', $request->quantity);
            SpareMovement::create([
                'spare_item_id'=>$spare->id,'work_order_id'=>$request->work_order_id,
                'quantity'=>-$request->quantity,'actor_id'=>auth()->id(),'note'=>'issue',
            ]);
        });
        return back();
    }
}
