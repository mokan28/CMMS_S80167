<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SpareItem extends Model { protected $fillable=['code','description','unit','quantity','reorder_level']; public function movements(){return $this->hasMany(SpareMovement::class);} }
