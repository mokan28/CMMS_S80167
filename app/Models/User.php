<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password','remember_token'];
    public function isChiefEngineer(): bool { return $this->role === 'chief_engineer'; }
    public function isTechnician(): bool { return $this->role === 'technician'; }
    public function isViewer(): bool { return $this->role === 'viewer'; }
}
