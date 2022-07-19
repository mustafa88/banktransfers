<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\bank\Enterprise;
use App\Models\bank\Projects;

class Donateworth extends Model
{
    use HasFactory;
    protected  $table = 'Donateworth';
    protected $fillable = ['id_donate','datedont','id_enter','id_proj','id_typedont','amount','description','namedont','created_at','updated_at'];
    protected $hidden = [];
    protected $primaryKey = 'id_donate';
    public $timestamps = true;

    public function enterprise(){
        return $this->belongsTo(Enterprise::class,'id_entrp','id');
    }

    public function projects(){
        return $this->belongsTo(Projects::class,'id_proj','id');
    }
}
