<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaintenancePlan extends Model
{
    protected $fillable = ['asset_id','title','trigger_type','interval_days','interval_hours','last_completion_date','last_completion_hours','source_label','status'];
    public function asset(){ return $this->belongsTo(Asset::class); }
    public function workOrders(){ return $this->hasMany(WorkOrder::class); }
}
