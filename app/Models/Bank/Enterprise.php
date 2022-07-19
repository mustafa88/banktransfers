<?php

namespace App\Models\bank;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\bank\Projects;
use App\Models\bank\Banks;
use App\Models\Bank\Campaigns;
use App\Models\Bank\Donateworth;

class Enterprise extends Model
{
    use HasFactory;

    protected  $table = 'enterprise';
    protected $fillable = ['id','name'];
    protected $hidden = [];
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function project(){
        return $this->hasMany(Projects::class,'id_entrp','id');
    }

    public function banks(){
        return $this->hasMany(Banks::class,'id_enter','id');
    }

    public function banksline(){
        return $this->hasMany(Banksline::class,'id_enter','id');
    }

    public function donateworth(){
        return $this->hasMany(Donateworth::class,'id_enter','id');
    }
}
