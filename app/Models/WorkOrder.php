<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkOrder extends Model
{
    protected $fillable = ['plan_id','state','assignee_id','opened_by','actual_completion_date','actual_completion_hours','completion_evidence','submitted_at','closed_at','closed_by','return_reason'];
    public function plan(){ return $this->belongsTo(MaintenancePlan::class); }
    public function assignee(){ return $this->belongsTo(User::class,'assignee_id'); }
}
