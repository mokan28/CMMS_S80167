<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Asset extends Model
{
    protected $fillable = ['vessel_id','code','name','subsystem','location','criticality','service_status','has_meter'];
    public function vessel(){ return $this->belongsTo(Vessel::class); }
    public function plans(){ return $this->hasMany(MaintenancePlan::class); }
}
