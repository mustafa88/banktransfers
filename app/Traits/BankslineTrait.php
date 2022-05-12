<?php

namespace App\Traits;


use App\Models\bank\Banksdetail;
use App\Models\bank\Banksline;

trait BankslineTrait
{
    /**
     * @param $id_line
     * עדכון שדה שורה תקינה בכותרת
     * שדה done=1 לכותרת
     */
    function checkFixLine($id_line)
    {
        $banksdetail_sum = banksdetail::select(
            \DB::raw("SUM(amountmandatory) as sum_amountmandatory"),
            \DB::raw("SUM(amountright) as sum_amountright")
        )
            ->where('id_line', $id_line)
            ->get()
            ->first();

        $banksline = Banksline::find($id_line);

        if(round($banksdetail_sum['sum_amountmandatory']-$banksline['amountmandatory'],2)==0
            and
            round($banksdetail_sum['sum_amountright']-$banksline['amountright'],2)==0
        ){
            $banksline->done=1;
        }
        else{
            $banksline->done=0;
        }

        $banksline->save();
    }
}
