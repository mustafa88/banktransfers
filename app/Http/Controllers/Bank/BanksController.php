<?php

namespace App\Http\Controllers\bank;

use App\Http\Controllers\Controller;
use App\Http\Requests\bank\BanksRequset;
use App\Models\bank\banks;
use App\Models\bank\Enterprise;
use Illuminate\Http\Request;

class BanksController extends Controller
{
    //
    public function showTable( $id = null){
        $bank = banks::with(['enterprise','projects'])->get();

        $enterprise = Enterprise::with('project')->get();
        $bankedt = null;
        if($id){
            $bankedt = banks::with(['enterprise','projects'])->find($id);
             //return $bankedt;
        }
        //return $bank;
        return view('manageabnk.listbanks', compact('bank','enterprise','bankedt'))
            ->with(
                [
                    'pageTitle' => "جدول البنوك",
                    'subTitle' => 'قائمة بجميع البنوك',
                ]
            );
    }

    public function showTableId($id_bank){

    }

    public function store(BanksRequset $requset ,$id_bank){

        $id_enterproj = explode('*', $requset->id_enterproj);

        $arrDate = [
            'banknumber' => $requset->banknumber,
            'bankbranch' => $requset->bankbranch,
            'bankaccount' => $requset->bankaccount,
            'id_enter' => $id_enterproj[0],
            'id_proj' => $id_enterproj[1],
        ];
        if($id_bank==0){
            Banks::create($arrDate);
        }else{
            Banks::where('id_bank', $id_bank)
                ->update($arrDate);
        }
        return redirect()->action([BanksController::class ,'showTable']);

        //return redirect()->back()->with("success", "تم الحفظ بنجاح");
    }
}
