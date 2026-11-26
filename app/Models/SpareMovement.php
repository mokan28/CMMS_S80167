<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SpareMovement extends Model { protected $fillable=['spare_item_id','work_order_id','quantity','actor_id','note']; }
