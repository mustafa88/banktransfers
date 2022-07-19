<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Controller;
use App\Models\bank\Enterprise;
use Illuminate\Http\Request;

class DonateworthController extends Controller
{
    //
    public function mainDonate()
    {
        //הצגת כל העמותות
        //$enterprise = Enterprise::with(['project'])->get()->toArray();
        $enterprise = Enterprise::with(['project'])->get();
        return view('manageabnk.donate' , compact('enterprise'))
            ->with(
                [
                    'pageTitle' => "تبرعات بقيمة",
                    'subTitle' => 'تسجيل تبرعات بقيمة للمؤسسات',
                ]
            );
    }
}
