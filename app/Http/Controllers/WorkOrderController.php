<?php
namespace App\Http\Controllers;
use App\Models\{WorkOrder,MaintenancePlan};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    public function store(Request $request)
    {
        $plan = MaintenancePlan::findOrFail($request->plan_id);
        // prevent duplicate active
        $existing = WorkOrder::where('plan_id',$plan->id)->whereIn('state',['open','in_progress','submitted'])->first();
        if ($existing) return back()->withErrors('Active work order already exists for this plan.');
        $wo = WorkOrder::create([
            'plan_id'=>$plan->id,'state'=>'open','opened_by'=>auth()->id(),
            'assignee_id'=>$request->assignee_id ?? auth()->id(),
        ]);
        return redirect()->route('workorders.show',$wo);
    }

    public function start(WorkOrder $workOrder)
    {
        if (!in_array($workOrder->state,['open'])) abort(422);
        if (!auth()->user()->isTechnician() && !auth()->user()->isChiefEngineer()) abort(403);
        if (auth()->user()->isTechnician() && $workOrder->assignee_id !== auth()->id()) abort(403);
        $workOrder->update(['state'=>'in_progress']);
        return back();
    }

    public function submit(Request $request, WorkOrder $workOrder)
    {
        if ($workOrder->state !== 'in_progress') abort(422);
        if (auth()->user()->isTechnician() && $workOrder->assignee_id !== auth()->id()) abort(403);
        $data = $request->validate([
            'actual_completion_date'=>'required|date',
            'actual_completion_hours'=>'nullable|integer|min:0',
            'completion_evidence'=>'required|string',
        ]);
        $workOrder->update(array_merge($data,['state'=>'submitted','submitted_at'=>now()]));
        return back();
    }

    public function returnToProgress(Request $request, WorkOrder $workOrder)
    {
        if (!$request->user()->isChiefEngineer()) abort(403);
        if ($workOrder->state !== 'submitted') abort(422);
        $request->validate(['return_reason'=>'required|string']);
        $workOrder->update(['state'=>'in_progress','return_reason'=>$request->return_reason]);
        return back();
    }

    public function close(Request $request, WorkOrder $workOrder)
    {
        if (!$request->user()->isChiefEngineer()) abort(403);
        if ($workOrder->state !== 'submitted') abort(422);
        if (!$workOrder->actual_completion_date) abort(422,'Missing completion data');

        DB::transaction(function () use ($workOrder) {
            $plan = $workOrder->plan;
            $workOrder->update(['state'=>'closed','closed_at'=>now(),'closed_by'=>auth()->id()]);
            $plan->update([
                'last_completion_date'=>$workOrder->actual_completion_date,
                'last_completion_hours'=>$workOrder->actual_completion_hours ?? $plan->last_completion_hours,
            ]);
        });
        return back();
    }
}
