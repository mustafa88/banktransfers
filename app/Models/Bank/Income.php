<?php

namespace App\Models\bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\bank\Projects;
class Income extends Model
{
    use HasFactory;
    protected  $table = 'income';
    protected $fillable = ['id','name'];
    protected $hidden = [];
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function projects(){
        return $this->belongsToMany(Projects::class,'projects_income','income_id','project_id','id','id');
    }

    public function banksdetail(){
        return $this->hasMany(Banksdetail::class,'id_incom','id');
    }
}
