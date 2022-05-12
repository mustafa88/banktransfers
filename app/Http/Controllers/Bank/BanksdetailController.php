<?php

namespace App\Http\Controllers\bank;

use App\Http\Controllers\Controller;
use App\Http\Requests\bank\BanksdetailRequset;
//use App\Models\bank\Banks;
use App\Models\bank\Banksline;
//use App\Models\bank\Enterprise;
//use App\Models\Bank\Title_one;
use App\Models\bank\Banksdetail;
use Illuminate\Http\Request;

use App\Traits\BankslineTrait;
class BanksdetailController extends Controller
{
    use BankslineTrait;
    public function showTable($id_line)
    {

        /**
        $bankslin = banksdetail::select(
            \DB::raw("SUM(amountmandatory) as sum_amountmandatory"),
            \DB::raw("SUM(amountright) as sum_amountright")
        )
            ->where('id_line', $id_line)
            ->get()
            ->first()
        ;
        return ($bankslin);
        **/

        $bankslin = Banksline::with([
            'banks',
            'titletwo',
            'enterprise.project',
            'enterprise.project.city',
            'banksdetail',
            'banksdetail.projects',
            'banksdetail.city',
            'banksdetail.income',
            'banksdetail.expense',
            'banksdetail.campaigns',
        ])->find($id_line);
        //return $bankslin;

        $project = $bankslin['enterprise']['project'];

        $projectCity = array();
        $allProject = $allCity = array();
        foreach ($project as $item){
            $allProject[$item['id']] =$item['name'];

            $projectCity[$item['id']]['name']=$item['name'];
            $projectCity[$item['id']]['city']=array();
            foreach ($item['city'] as $item2){
                $allCity[$item2['city_id']] =$item2['city_name'];

                $projectCity[$item['id']]['city'][$item2['city_id']]=$item2['city_name'];
            }
        }
        //print_r($allProject);
        //print_r($allCity);
        //return($projectCity);
        $msginfo = $this->msgInfo($id_line);
        return view('manageabnk.detailbanks', compact('bankslin','allProject','allCity','projectCity' ,'msginfo'))
            ->with(
                [
                    'pageTitle' => "פירוט תנועה בבנק",
                    'subTitle' => 'פירוט תנועות חשבון בבנק',
                ]
            );
    }

    /**
     * @param $id_line
     * @param $id_detail
     * @param $typeValue = 0 ערך טקסט אחרת ערך מספרי
     */
    public function getRowBy($id_line, $id_detail ,$typeValue){

        $bankslin = Banksline::with([
            'banks',
            'titletwo',
            'enterprise.project',
            'banksdetail' => function ($query) use ($id_detail) {
                $query->where('id_detail', '=' ,$id_detail);
            },
            'banksdetail.projects',
            'banksdetail.city',
            'banksdetail.income',
            'banksdetail.expense',
            'banksdetail.campaigns',
        ])->find($id_line);

        $rowTable = array();
        $rowTable['id_detail'] = $bankslin['banksdetail'][0]['id_detail'];

        $rowTable['e'] = $bankslin['banksdetail'][0]['note'];
        $rowTable['d'] = '';


        if($typeValue=='1'){
            //ערך מספרי -
            $rowTable['b'] =$bankslin['banksdetail'][0]['projects']['id'] ;
            $rowTable['c'] = $bankslin['banksdetail'][0]['city']['city_id'];
            $rowTable['campn'] =0;
            if(isset($bankslin['banksdetail'][0]['campaigns']['id'])){
                $rowTable['campn'] = $bankslin['banksdetail'][0]['campaigns']['id'];
            }

        }else{
            //$typeValue=='0' ערך טקסט
            $rowTable['b'] =$bankslin['banksdetail'][0]['projects']['name'] ;
            $rowTable['c'] = $bankslin['banksdetail'][0]['city']['city_name'];
            $rowTable['campn'] ='';
            if(isset($bankslin['banksdetail'][0]['campaigns']['name_camp'])){
                $rowTable['campn'] = $bankslin['banksdetail'][0]['campaigns']['name_camp'];
            }
        }

            if($bankslin['amountmandatory']=='0'){

                $rowTable['a'] = $bankslin['banksdetail'][0]['amountright'];

                if($typeValue=='1'){
                    //ערך מספרי -
                    if(!empty($bankslin['banksdetail'][0]['income']['id'])){
                        $rowTable['d'] = $bankslin['banksdetail'][0]['income']['id'] ;
                    }
                }else{
                    //$typeValue=='0' ערך טקסט
                    if(!empty($bankslin['banksdetail'][0]['income']['name'])){
                        $rowTable['d'] = $bankslin['banksdetail'][0]['income']['name'] ;
                    }
                }
            }else{
                $rowTable['a'] = $bankslin['banksdetail'][0]['amountmandatory'];
                if($typeValue=='1'){
                    //ערך מספרי -
                    if(!empty($bankslin['banksdetail'][0]['expense']['id'])){
                        $rowTable['d'] = $bankslin['banksdetail'][0]['expense']['id'] ;
                    }
                }else{
                    //$typeValue=='0' ערך טקסט
                    if(!empty($bankslin['banksdetail'][0]['expense']['name'])){
                        $rowTable['d'] = $bankslin['banksdetail'][0]['expense']['name'] ;
                    }
                }
            }

        return $rowTable;

    }


    public function editAjax($id_line, $id_detail)
    {
        $bankslin = Banksline::with([
            'banks',
            'titletwo',
            'enterprise.project',
            'banksdetail' => function ($query) use ($id_detail) {
                $query->where('id_detail', '=' ,$id_detail);
            },
            'banksdetail.projects',
            'banksdetail.city',
            'banksdetail.income',
            'banksdetail.expense',
            'banksdetail.campaigns',
        ])->find($id_line);

        if (!$bankslin) {
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'תקלה - שורה לא קיימת';
            return response()->json($resultArr);
        }

        $rowedit = $this->getRowBy($id_line, $id_detail ,$typeValue= 1);

        $resultArr['status'] = true;
        $resultArr['cls'] = 'info';
        $resultArr['msg'] = 'עריכת שורה';
        $resultArr['row'] = $rowedit;
        return response()->json($resultArr);
    }

    public function updateAjax(BanksdetailRequset $requset, $id_line, $id_detail)
    {

        $banksdetail = Banksdetail::where('id_line','=',$id_line)->find($id_detail);
        if(!$banksdetail){
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'שורת בנק לא קיימת';
            return response()->json($resultArr);
        }

        $banksdetail['id_proj'] = $requset->proj;
        $banksdetail['id_city'] = $requset->city;
        $banksdetail['note'] = $requset->note;

        if($requset->id_campn==0){
            $banksdetail['id_campn'] = null;
        }else{
            $banksdetail['id_campn'] = $requset->id_campn;
        }

        $sum_amountmandatory_hefrsh=0;
        $sum_amountright_hefrsh=0;
        if($banksdetail['amountmandatory']==0){
            // זכות - סוג הכנסה
            $sum_amountright_hefrsh = $requset->scome - $banksdetail['amountright'];
            $banksdetail['amountright']=$requset->scome;
            if($requset->incmexpe!=0){
                $banksdetail['id_incom']=$requset->incmexpe;
            }


            $banksdetail['amountmandatory']=0;
        }else{
            //חובה וסוג הוצאה

             $bankslin = Banksline::find($id_line);
             if($bankslin['id_titletwo']=='2' and $requset->incmexpe=='0'){
              //הוצאה מסוג תשלום לספק חייב  שיבחר שם ספק מרשימה לא ניתן שיהיה ריק
                 $resultArr['status'] = false;
                 $resultArr['cls'] = 'error';
                 $resultArr['msg'] = 'חובה לבחור ספק';
                 return response()->json($resultArr);
             }

            $sum_amountmandatory_hefrsh = $requset->scome - $banksdetail['amountmandatory'];

            $banksdetail['amountmandatory']=$requset->scome;
            if($requset->incmexpe!=0){
                $banksdetail['id_expens']=$requset->incmexpe;
            }
            $banksdetail['amountright']=0;
        }

        $bankslinxx = Banksline::find($id_line);


        $bankslin_sum = banksdetail::select(
            \DB::raw("SUM(amountmandatory) as sum_amountmandatory"),
            \DB::raw("SUM(amountright) as sum_amountright")
        )
            ->where('id_line', $id_line)
            ->get()
            ->first();

        if((round($bankslin_sum['sum_amountmandatory']+$sum_amountmandatory_hefrsh,2)>$bankslinxx['amountmandatory'])
            or
            (round($bankslin_sum['amountright']+$sum_amountright_hefrsh,2)>$bankslinxx['amountright'])
        ){
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'סך הכל שורות גדול מסכום שורה ראשית' . ($bankslin_sum['sum_amountmandatory']+$sum_amountmandatory_hefrsh);
            return response()->json($resultArr);
        }



        $banksdetail->save();
        $this->checkFixLine($id_line);
        $resultArr['row'] = $this->getRowBy($id_line, $id_detail ,$typeValue= 0);

        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['msg'] = 'נשמחר בהצלחה';
        $resultArr['msginfo'] = $this->msgInfo($id_line);
        return response()->json($resultArr);
    }

    /**
     * @param Requset $requset
     * @param $id_line מספר שורה
     * שמירה חלוקת השורות
     */
    public function storeDivDetail(Request $requset, $id_line)
    {
        //return $requset;

        /**
         * מחיקת כל השורות הפירוט במידה וקיים
         * שמירת שורות חדשות
         */

        //$resultArr['status'] = false;
        //$resultArr['cls'] = 'error';
        //$resultArr['msg'] = 'שורת בנק לא קיימת';
        //return response()->json($resultArr);

        $bankslin = Banksline::with(['banks', 'titletwo', 'enterprise.project','banksdetail'])
            ->find($id_line);

        //->where('id_line', '=', $id_line)->get();
        if(!$bankslin){
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'שורת בנק לא קיימת';
            return $resultArr;
        }

        if($bankslin['id_titletwo']=='2' ){
            //הוצאה מסוג תשלום לספק חייב  שיבחר שם ספק מרשימה לא ניתן שיהיה ריק
            return redirect()->back()->with("success", 'חובה לבחור ספק');
        }
        $datapost = $requset->all();


        //return $datapost['dcom*4*1'];
        $sumInput = 0;
        foreach ($datapost as $key => $value){
            if(substr($key,0,4)=='dcom'){
                $sumInput += $value;
            }
        }
        //var_dump($sumInput);
        //var_dump($bankslin['amountmandatory']);
        //var_dump($bankslin['amountright']);
        //return ($requset);
        if(round($sumInput - ($bankslin['amountmandatory'] + $bankslin['amountright']),2)!=0){
            //$x = round($sumInput - ($bankslin['amountmandatory'] + $bankslin['amountright']),2);
            return redirect()->back()->with("success", "שגיאה - סך הכל חלוקה לא שווה לסכום השורה");
        }

        $banksdetail = Banksdetail::where('id_line', '=', $id_line)->get();
        //return $banksdetail;
        if($banksdetail){
            //מחיקת כל השורות במידה וקקים
            //$banksdetail->delete();
            \DB::table('Banksdetail')->where('id_line', '=', $id_line)->delete();
        }

        $arrDate = [
            'id_line' => $id_line,
        ];

        if($bankslin['amountmandatory']==0){
            // זכות - סוג הכנסה
            $arrDate['amountmandatory']=0;
        }else{
            // חובה וסוג הוצאה
            $arrDate['amountright']=0;
        }

        foreach ($datapost as $key => $value){
            if($value==0){
                //ערך שוה שווה לאפס מדלגים
                continue;
            }
            if(substr($key,0,4)=='dcom'){
                $temp = explode("*",$key);
                $arrDate['id_proj']=$temp[1];
                $arrDate['id_city']=$temp[2];
                if($bankslin['amountmandatory']==0){
                    //זכות - סוג הכנסה
                    $arrDate['amountright']=$value;
                }else{
                    //חובה וסוג הוצאה
                    $arrDate['amountmandatory']=$value;

                }
                //RETURN $arrDate;
                Banksdetail::create($arrDate);
            }
        }

        $this->checkFixLine($id_line);

        return redirect()->back()->with("success", "בוצעה חלוקה בהצלחה");
    }


    public function storeAjax(BanksdetailRequset $requset, $id_line)
    {
        $bankslin = Banksline::with(['banks', 'titletwo', 'enterprise.project','banksdetail'])
            ->find($id_line);
            //->where('id_line', '=', $id_line)
            //->get();
        if(!$bankslin){
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'שורת בנק לא קיימת';
            return response()->json($resultArr);
        }

        $bankslin_sum = banksdetail::select(
            \DB::raw("SUM(amountmandatory) as sum_amountmandatory"),
            \DB::raw("SUM(amountright) as sum_amountright")
        )
            ->where('id_line', $id_line)
            ->get()
            ->first();

        $arrDate = [
            'id_line' => $id_line,
            'id_proj' => $requset->proj,
            'id_city' => $requset->city,
            'note' => $requset->note,
        ];

        if($requset->id_campn!=0){
            $arrDate['id_campn']=$requset->id_campn;
        }

        if($bankslin['amountmandatory']==0){
            // זכות - סוג הכנסה
            $arrDate['amountright']=$requset->scome;
            if($requset->incmexpe!=0){
                $arrDate['id_incom']=$requset->incmexpe;
            }

            $arrDate['amountmandatory']=0;
        }else{
            // חובה וסוג הוצאה

            if($bankslin['id_titletwo']=='2' and $requset->incmexpe=='0'){
                //הוצאה מסוג תשלום לספק חייב  שיבחר שם ספק מרשימה לא ניתן שיהיה ריק
                $resultArr['status'] = false;
                $resultArr['cls'] = 'error';
                $resultArr['msg'] = 'חובה לבחור ספק';
                return response()->json($resultArr);
            }

            $arrDate['amountmandatory']=$requset->scome;
            if($requset->incmexpe!=0){
                $arrDate['id_expens']=$requset->incmexpe;
            }

            $arrDate['amountright']=0;
        }


        if((round($bankslin_sum['sum_amountmandatory']+$arrDate['amountmandatory'],2)>$bankslin['amountmandatory'])
            or
            (round($bankslin_sum['sum_amountright']+$arrDate['amountright'],2)>$bankslin['amountright'])
        ){
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'סך הכל שורות גדול מסכום שורה ראשית';
            return response()->json($resultArr);
        }

        $rowinsert = Banksdetail::create($arrDate);

        $this->checkFixLine($id_line);

        //מהחזיר את השורה ולהציג אותה במסך
        $rownew = $this->getRowBy($id_line, $rowinsert['id_detail'] ,$typeValue= 0);


        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['msg'] = 'נשמחר בהצלחה';
        $resultArr['row'] = $rownew;
        $resultArr['msginfo'] = $this->msgInfo($id_line);
        return response()->json($resultArr);

    }

    /**
     * @param BanksdetailRequset $requset
     * @param $id_bank
     * @param $id_line
     * שמירה שורה - חלוקת שורה
     */
    public function storeMultiRowAjax(BanksdetailRequset $requset, $id_bank, $id_line)
    {
        /**
         * מחיקת כל החלוקה לשורה
         * כתיבת שורה פיירוט

        $resultArr['status'] = true;
        $resultArr['cls'] = 'error';
        $resultArr['msg'] = 'שורת בנק לא קיימת';
        return response()->json($resultArr);
         */

        $bankslin = Banksline::with(['banks', 'titletwo', 'enterprise.project','banksdetail'])
            ->where('id_bank', '=', $id_bank)
            ->find($id_line);
        if(!$bankslin){
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'שורת בנק לא קיימת';
            return response()->json($resultArr);
        }

        $banksdetail = Banksdetail::where('id_line','=',$id_line)->delete();
        $this->checkFixLine($id_line);

        /**
        $resultArr['status'] = false;
        $resultArr['cls'] = 'error';
        $resultArr['msg'] = 'שורת בנק לא קיימת';

        $resultArr['a'] = $bankslin;
        $resultArr['b'] = $banksdetail;
        return response()->json($resultArr);
        **/

        $arrDate = [
            'id_line' => $id_line,
            'id_proj' => $requset->proj,
            'id_city' => $requset->city,
            'note' => $requset->note,
        ];

        if($requset->id_campn!=0){
            $arrDate['id_campn']=$requset->id_campn;
        }

        $arrDate['amountright']=$bankslin['amountright'];
        $arrDate['amountmandatory']=$bankslin['amountmandatory'];
        if($bankslin['amountmandatory']==0){
            // זכות - סוג הכנסה
            if($requset->incmexpe!=0){
                $arrDate['id_incom']=$requset->incmexpe;
            }
        }else{
            // חובה וסוג הוצאה
            if($bankslin['id_titletwo']=='2' and $requset->incmexpe=='0'){
                //הוצאה מסוג תשלום לספק חייב  שיבחר שם ספק מרשימה לא ניתן שיהיה ריק
                $resultArr['status'] = false;
                $resultArr['cls'] = 'error';
                $resultArr['msg'] = 'חובה לבחור ספק';
                return response()->json($resultArr);
            }
            if($requset->incmexpe!=0){
                $arrDate['id_expens']=$requset->incmexpe;
            }
        }

        $rowinsert = Banksdetail::create($arrDate);

        $this->checkFixLine($id_line);

        //מהחזיר את השורה ולהציג אותה במסך
        //$rownew = $this->getRowBy($id_line, $rowinsert['id_detail'] ,$typeValue= 0);


        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['msg'] = 'נשמחר בהצלחה';
        //$resultArr['row'] = $rownew;
        //$resultArr['msginfo'] = $this->msgInfo($id_line);
        return response()->json($resultArr);
    }

    public function deleteAjax($id_line, $id_detail)
    {

        $banksdetail = Banksdetail::where('id_line','=',$id_line)->find($id_detail);
        if(!$banksdetail){
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'שורת בנק לא קיימת';
            return response()->json($resultArr);
        }

        $banksdetail->delete();

        $this->checkFixLine($id_line);

        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['msg'] = 'נמחק בהצלחה';
        $resultArr['msginfo'] = $this->msgInfo($id_line);
        return response()->json($resultArr);
    }


    public function msgInfo($id_line)
    {

      //  $banksdetail_sum = banksdetail::where('id_line', $id_line)
       //     ->get()
        //    ;

        $banksdetail_sum = banksdetail::select(
            \DB::raw("SUM(amountmandatory) as sum_amountmandatory"),
            \DB::raw("SUM(amountright) as sum_amountright")
        )
            ->where('id_line', $id_line)
            ->get()
            ->first();

        $banksline = Banksline::find($id_line);
        //echo $banksdetail_sum;
        //exit;
        //ddd($banksdetail_sum);
        //ddd($banksdetail_sum);
        if($banksdetail_sum['sum_amountmandatory']==null){

            if($banksline['amountmandatory']>0){
                $x  = $banksline['amountmandatory'] ;

            }else{
                $x  = $banksline['amountright'];
            }
        }else{

            if($banksdetail_sum['sum_amountmandatory']>0){
                //echo $banksdetail_sum['sum_amountmandatory'];
                $x  = round($banksline['amountmandatory'] - $banksdetail_sum['sum_amountmandatory'],2);
            }else{
                //echo '2';
                $x  = round($banksline['amountright'] - $banksdetail_sum['sum_amountright'],2);
            }
        }
        //ddd($x);
        if($x==0){
            return "שורה תקינה - בוצעה חלוקה שלמה";
        }
        return "נותר לחלק {$x} ש\"ח ";
    }
}
