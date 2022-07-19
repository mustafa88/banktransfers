<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Controller;
use App\Models\bank\Banks;
use App\Models\bank\Banksline;
use App\Models\bank\City;
use App\Models\bank\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportProjectController extends Controller
{
    public function mainPageProj(Request $requset){
        //var_dump($requset->fdate);

        //if(!empty($requset->fdate)){
        $reports = array();
        if($requset->has('fdate')){
            $enterp_proj =$requset->enterp;
            $fromDate =$requset->fdate;
            $toDate=$requset->tdate;
            $enterp_proj = explode("*" , $enterp_proj);
            $enterp = $enterp_proj[0];
            $proj = $enterp_proj[1];
            $city =$requset->city;
            /**
            $id_bank =1;
            $fromDate ='2021-01-01';
            $toDate='2021-12-31';
             */

            $r1 = $this->xreport1($enterp ,$fromDate ,$toDate);
            $reports[] = 'r1';

            $r2 = $this->xreport2($enterp ,$fromDate ,$toDate ,$type=0 ,$proj);
            $reports[] = 'r2';


            $r3 = $this->xreport3($enterp ,$fromDate ,$toDate,$type=0 ,$proj ,$city);
            $reports[] = 'r3';



            $r4_out = $this->xreport4($enterp ,$fromDate ,$toDate ,'1' ,$proj ,$city);
            $reports[] = 'r4_out';
            $r5_out = $this->xreport5($enterp ,$fromDate ,$toDate ,'1' ,$proj ,$city);
            $reports[] = 'r5_out';
            $r6_out = $this->xreport6($enterp ,$fromDate ,$toDate ,'1' ,$proj ,$city);
            $reports[] = 'r6_out';
            $r7_out = $this->xreport7($enterp ,$fromDate ,$toDate ,'1' ,$proj ,$city);
            $reports[] = 'r7_out';

            $r4_in = $this->xreport4($enterp ,$fromDate ,$toDate ,'2' ,$proj ,$city);
            $reports[] = 'r4_in';
            $r5_in = $this->xreport5($enterp ,$fromDate ,$toDate ,'2' ,$proj ,$city);
            $reports[] = 'r5_in';
            $r6_in = $this->xreport6($enterp ,$fromDate ,$toDate ,'2' ,$proj ,$city);
            $reports[] = 'r6_in';
            $r7_in = $this->xreport7($enterp ,$fromDate ,$toDate ,'2' ,$proj ,$city);
            $reports[] = 'r7_in';


            //return $r10_out;
        }else{
            $requset->fdate =  date('Y-01-01');
            $requset->tdate =  date('Y-12-31') ;
        }


        
        $enterprise = Enterprise::with(['project'])->get();
        $city = City::get();
        //return $city;
        return view('reports.reportproject' , compact('enterprise','city',$reports))
            ->with(
                [
                    'pageTitle' => "تقارير بنكيه",
                    'subTitle' => 'بناء تقارير بنكية',
                ]
            );

    }

    function xreport1($enterp ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp'
                ,DB::raw("round(sum(banksline.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksline.amountright),2) as amountright")
                ,DB::raw("round(sum(banksline.amountright-banksline.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')

            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name')
            ->orderBy('enterprise.name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();
        return json_decode(json_encode($result), true);
        //return $result;
    }

    function xreport2($enterp ,$fromDate ,$toDate ,$type=0 ,$proj=0){
        /*
         * עמותה
         * פרויקט
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            );


        $result->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')

            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate);
            if($proj!='0'){
                $result->where('projects.id', $proj);
            }

        $result->groupBy('enterprise.name','projects.name')

            ->orderBy('enterprise.name')
            ->orderBy('projects.name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();
        return json_decode(json_encode($result), true);
        //return $result;
    }

    function xreport3($enterp ,$fromDate ,$toDate ,$type=0 ,$proj=0 ,$city=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate);
            if($proj!='0'){
                $result->where('projects.id', $proj);
            }
        if($city!='0'){
            $result->where('city.city_id', $city);
        }
        $result->groupBy('enterprise.name','projects.name','city.city_name')

            ->orderBy('enterprise.name')
            ->orderBy('projects.name')
            ->orderBy('city.city_name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();
        return json_decode(json_encode($result), true);
        //return $result;
    }

    function xreport4($enterp ,$fromDate ,$toDate ,$type=0 ,$proj=0 ,$city=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * סוג תנועה
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name','title_two.ttwo_text'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate);
             if($proj!='0'){
                 $result->where('projects.id', $proj);
             }
        if($city!='0'){
            $result->where('city.city_id', $city);
        }

        $result->groupBy('enterprise.name','title_two.ttwo_text','projects.name','city.city_name')

            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name')
            ->orderBy('city.city_name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();
        return json_decode(json_encode($result), true);
        //return $result;
    }

    function xreport5($enterp ,$fromDate ,$toDate ,$type=0 ,$proj=0 ,$city=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * סוג תנועה
         * סוג חובה / זכות
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name','title_two.ttwo_text'
                //,'expense.name as expensename'
                //,'income.name as incomename'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->leftJoin('expense', 'expense.id', '=', 'banksdetail.id_expens')
            ->leftJoin('income', 'income.id', '=', 'banksdetail.id_incom')

            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate);

            if($proj!='0'){
                $result->where('projects.id', $proj);
            }
        if($city!='0'){
            $result->where('city.city_id', $city);
        }

        $result->groupBy('enterprise.name','title_two.ttwo_text','projects.name','city.city_name')
            //->groupBy('expense.name','income.name')
            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name')
            ->orderBy('city.city_name');
            //->orderBy('expense.name')
            //->orderBy('income.name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            //$result->whereNotNull('expense.name');
            //פירוט רק עבור תשלומים לספקים
            $result->where('banksline.id_titletwo' ,2);

            $result->addSelect('expense.name as expensename')
                ->groupBy('expense.name')
                ->orderBy('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            //$result->whereNotNull('income.name');

            $result->addSelect('income.name as incomename')
                ->groupBy('income.name')
                ->orderBy('income.name');

        }

        $result = $result->get();
        return json_decode(json_encode($result), true);
        //return $result;
    }

    function xreport6($enterp ,$fromDate ,$toDate ,$type=0 ,$proj=0 ,$city=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * סוג תנועה
         * סוג חובה / זכות
         * חודש
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name','title_two.ttwo_text'
                //,'expense.name as expensename'
                //,'income.name as incomename'
                ,DB::raw("(DATE_FORMAT(datemovement, '%m-%Y')) as month_year")
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->leftJoin('expense', 'expense.id', '=', 'banksdetail.id_expens')
            ->leftJoin('income', 'income.id', '=', 'banksdetail.id_incom')

            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate);

        if($proj!='0'){
            $result->where('projects.id', $proj);
        }
        if($city!='0'){
            $result->where('city.city_id', $city);
        }

        $result->groupBy('enterprise.name','title_two.ttwo_text','projects.name','city.city_name')
            //->groupBy('expense.name','income.name')
            ->groupBy(DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"))

            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name')
            ->orderBy('city.city_name')
            //->orderBy('expense.name')
            //->orderBy('income.name')
            ->orderBy(DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"));

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            //$result->whereNotNull('expense.name');
            //פירוט רק עבור תשלומים לספקים
            $result->where('banksline.id_titletwo' ,2);

            $result->addSelect('expense.name as expensename')
                ->groupBy('expense.name')
                ->orderBy('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            //$result->whereNotNull('income.name');

        }

        $result = $result->get();
        return json_decode(json_encode($result), true);
        //return $result;
    }

    function xreport7($enterp ,$fromDate ,$toDate ,$type=0 ,$proj=0 ,$city=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * קמפיין
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name','campaigns.name_camp'

                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')
            ->leftJoin('campaigns', 'campaigns.id', '=', 'banksdetail.id_campn')


            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->whereNotNull('banksdetail.id_campn');

        if($proj!='0'){
            $result->where('projects.id', $proj);
        }
        if($city!='0'){
            $result->where('city.city_id', $city);
        }

        $result->groupBy('enterprise.name', 'projects.name','city.city_name' ,'campaigns.name_camp')

            ->orderBy('enterprise.name')
            ->orderBy('projects.name')
            ->orderBy('city.city_name')
            ->orderBy('campaigns.name_camp');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            //$result->whereNotNull('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            //$result->whereNotNull('income.name');
        }

        $result = $result->get();
        return json_decode(json_encode($result), true);
        //return $result;
    }


    function xxreport1($enterp ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * סוג תנועה
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name','title_two.ttwo_text'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->where('id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name','title_two.ttwo_text','projects.name','city.city_name')

            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name')
            ->orderBy('city.city_name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();

        return $result;
    }


    function report1($enterp ,$fromDate ,$toDate){

        /**1
        עמתה
        מספר שורות
        סך הכל חובה
        סל הכל זכות
        סך הכל
         */
        $result = Banksline::
        select(
            'id_enter',
            \DB::raw("Count(*) as count_row"),
            \DB::raw("round(sum(amountmandatory),2) as amountmandatory"),
            \DB::raw("round(sum(amountright),2) as amountright"),
            \DB::raw("round(sum(amountright-amountmandatory),2) as total_neto"),
        )
            ->with(['enterprise'])
            ->where('id_enter', $enterp)
            ->where('datemovement','>=' ,$fromDate)
            ->where('datemovement','<=' ,$toDate)
            ->groupBy('id_enter')
            ->get()
        ;
        return $result;
    }

    function report2($id_bank ,$fromDate ,$toDate ,$type=0){
        /**2
        חודש
        סך הכל חובה
        סל הכל זכות
        סך הכל
         */
        /**
         * $type=0 - חובה - זכות
         * $type=1 - חובה
         * $type=2 - זכות
         */
        $result = Banksline::
        select(
            'id_enter',
            \DB::raw("(DATE_FORMAT(datemovement, '%m-%Y')) as month_year"),
            \DB::raw("Count(*) as count_row"),
            \DB::raw("round(sum(amountmandatory),2) as amountmandatory"),
            \DB::raw("round(sum(amountright),2) as amountright"),
            \DB::raw("round(sum(amountright-amountmandatory),2) as total_neto"),
        )
            ->with(['enterprise'])
            ->where('id_bank', $id_bank)
            ->where('datemovement','>=' ,$fromDate)
            ->where('datemovement','<=' ,$toDate)
            ->groupBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"))
            ->groupBy('id_enter')
            ->orderBy('id_enter')
            ->orderBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"));

        if($type==1){
            // רק חובה
            $result->where('amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('amountright',"!=" ,0);
        }

        $result = $result->get();

        ;
        return $result;
    }

    function report3($id_bank ,$fromDate ,$toDate ,$type=0){
        /**3
        סוג תנועה
        סך הכל חובה
        סל הכל זכות
        סך הכל
         */
        $result = Banksline::
        select(
            'id_enter',
            'id_titletwo',
            \DB::raw("Count(*) as count_row"),
            \DB::raw("round(sum(amountmandatory),2) as amountmandatory"),
            \DB::raw("round(sum(amountright),2) as amountright"),
            \DB::raw("round(sum(amountright-amountmandatory),2) as total_neto"),
        )
            ->with(['enterprise' ,'titletwo'])
            ->where('id_bank', $id_bank)
            ->where('datemovement','>=' ,$fromDate)
            ->where('datemovement','<=' ,$toDate)
            ->groupBy('id_enter')
            ->groupBy('id_titletwo');

        if($type==1){
            // רק חובה
            $result->where('amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('amountright',"!=" ,0);
        }

        $result = $result->get();

        ;
        return $result;
    }

    function report4($id_bank ,$fromDate ,$toDate ,$type=0){
        /**4
        חודש
        סוג תנועה
        סך הכל חובה
        סל הכל זכות
        סך הכל
         */
        $result = Banksline::
        select(
            'id_enter',
            'id_titletwo',
            \DB::raw("(DATE_FORMAT(datemovement, '%m-%Y')) as month_year"),
            \DB::raw("Count(*) as count_row"),
            \DB::raw("round(sum(amountmandatory),2) as amountmandatory"),
            \DB::raw("round(sum(amountright),2) as amountright"),
            \DB::raw("round(sum(amountright-amountmandatory),2) as total_neto"),
        )
            ->with(['enterprise' ,'titletwo'])
            ->where('id_bank', $id_bank)
            ->where('datemovement','>=' ,$fromDate)
            ->where('datemovement','<=' ,$toDate)
            ->groupBy('id_enter')
            ->groupBy('id_titletwo')
            ->groupBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"))
            ->orderBy('id_enter')
            ->orderBy('id_titletwo')
            ->orderBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"));

        if($type==1){
            // רק חובה
            $result->where('amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('amountright',"!=" ,0);
        }

        $result = $result->get();

        return $result;
    }

    function report5($id_bank ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * סוג תנועה
         * פרויקט
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','title_two.ttwo_text','projects.name as proj'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')


            ->where('banksline.id_bank','=' ,$id_bank)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name','title_two.ttwo_text','projects.name')

            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();

        return $result;
    }

    function report6($id_bank ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * סוג תנועה
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name','title_two.ttwo_text'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->where('banksline.id_bank','=' ,$id_bank)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name','title_two.ttwo_text','projects.name','city.city_name')

            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name')
            ->orderBy('city.city_name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();

        return $result;
    }


    function report7($enterp ,$fromDate ,$toDate ,$type=0){
        /**7
         * עמותה
         * פרויקט
         * סך הכל חובה
         * סל הכל זכות
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')


            ->where('banksline.id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name','projects.name')

            ->orderBy('enterprise.name')
            ->orderBy('projects.name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();

        return $result;
    }

    function report8($enterp ,$fromDate ,$toDate ,$type=0){
        /**8
         * עמותה
         * פרויקט
         * עיר
         * סך הכל חובה
         * סל הכל זכות
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->where('banksline.id_enter', $enterp)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name','projects.name','city.city_name')

            ->orderBy('enterprise.name')
            ->orderBy('projects.name')
            ->orderBy('city.city_name');

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
        }

        $result = $result->get();

        return $result;
    }



    function report9($id_bank ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * סוג תנועה
         * פרויקט
         * עיר
         * סוג חובה/סוג זכות
         * חובה
         * זכות
         * נטו
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','title_two.ttwo_text','projects.name as proj'
                ,'city.city_name'
                ,'expense.name as expensename'
                ,'income.name as incomename'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->leftJoin('expense', 'expense.id', '=', 'banksdetail.id_expens')
            ->leftJoin('income', 'income.id', '=', 'banksdetail.id_incom')

            ->where('banksline.id_bank','=' ,$id_bank)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name','title_two.ttwo_text','projects.name'
                ,'city.city_name','expense.name','income.name')

            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name')
            ->orderBy('city.city_name')
            ->orderBy('expense.name')
            ->orderBy('income.name')
        ;

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            $result->whereNotNull('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            $result->whereNotNull('income.name');
        }

        $result = $result->get();

        return $result;
    }

    function report10($id_bank ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * סוג תנועה
         * פרויקט
         * עיר
         * סוג חובה/סוג זכות
         * חודש
         * חובה/זכות
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','title_two.ttwo_text','projects.name as proj'
                ,'city.city_name'
                ,'expense.name as expensename'
                ,'income.name as incomename'
                ,\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y')) as month_year")
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->leftJoin('expense', 'expense.id', '=', 'banksdetail.id_expens')
            ->leftJoin('income', 'income.id', '=', 'banksdetail.id_incom')

            ->where('banksline.id_bank','=' ,$id_bank)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->groupBy('enterprise.name','title_two.ttwo_text','projects.name'
                ,'city.city_name','expense.name','income.name')
            ->groupBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"))

            ->orderBy('enterprise.name')
            ->orderBy('title_two.ttwo_text')
            ->orderBy('projects.name')
            ->orderBy('city.city_name')
            ->orderBy('expense.name')
            ->orderBy('income.name')
            ->orderBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"))
        ;

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            $result->whereNotNull('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            $result->whereNotNull('income.name');
        }

        $result = $result->get();

        return $result;
    }


    function report11($id_bank ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * פרויקט
         * קמפיין
         * חובה/זכות
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'campaigns.name_camp'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->leftJoin('expense', 'expense.id', '=', 'banksdetail.id_expens')
            ->leftJoin('income', 'income.id', '=', 'banksdetail.id_incom')

            ->leftJoin('campaigns', 'campaigns.id', '=', 'banksdetail.id_campn')

            ->where('banksline.id_bank','=' ,$id_bank)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->whereNotNull('campaigns.name_camp')
            ->groupBy('enterprise.name','projects.name'

            )
            ->groupBy('campaigns.name_camp')


            ->orderBy('enterprise.name')
            ->orderBy('projects.name')
        ;

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            $result->whereNotNull('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            $result->whereNotNull('income.name');
        }

        $result = $result->get();

        return $result;
    }

    function report12($id_bank ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * פרויקט
         * חודש
         * קמפיין
         * חובה/זכות
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                //,'city.city_name'
                ,\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y')) as month_year")
                ,'campaigns.name_camp'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->leftJoin('expense', 'expense.id', '=', 'banksdetail.id_expens')
            ->leftJoin('income', 'income.id', '=', 'banksdetail.id_incom')

            ->leftJoin('campaigns', 'campaigns.id', '=', 'banksdetail.id_campn')

            ->where('banksline.id_bank','=' ,$id_bank)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->whereNotNull('campaigns.name_camp')
            ->groupBy('enterprise.name','projects.name'
            )
            ->groupBy('campaigns.name_camp')
            ->groupBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"))


            ->orderBy('enterprise.name')
            ->orderBy('projects.name')
            ->orderBy(\DB::raw("(DATE_FORMAT(datemovement, '%m-%Y'))"))
        ;

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            $result->whereNotNull('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            $result->whereNotNull('income.name');
        }

        $result = $result->get();

        return $result;
    }

    function report13($id_bank ,$fromDate ,$toDate ,$type=0){
        /*
         * עמותה
         * פרויקט
         * עיר
         * קמפיין
         * חובה/זכות
         */
        $result = DB::table('banksline')
            ->select('enterprise.name as enterp','projects.name as proj'
                ,'city.city_name'
                ,'campaigns.name_camp'
                ,DB::raw("round(sum(banksdetail.amountmandatory),2) as amountmandatory")
                ,DB::raw("round(sum(banksdetail.amountright),2) as amountright")
                ,DB::raw("round(sum(banksdetail.amountright-banksdetail.amountmandatory),2) as total_neto")
            )
            ->leftJoin('enterprise', 'enterprise.id', '=', 'banksline.id_enter')
            ->leftJoin('title_two', 'title_two.ttwo_id', '=', 'banksline.id_titletwo')

            ->leftJoin('banksdetail', 'banksdetail.id_line', '=', 'banksline.id_line')

            ->leftJoin('projects', 'projects.id', '=', 'banksdetail.id_proj')
            ->leftJoin('city', 'city.city_id', '=', 'banksdetail.id_city')

            ->leftJoin('expense', 'expense.id', '=', 'banksdetail.id_expens')
            ->leftJoin('income', 'income.id', '=', 'banksdetail.id_incom')

            ->leftJoin('campaigns', 'campaigns.id', '=', 'banksdetail.id_campn')

            ->where('banksline.id_bank','=' ,$id_bank)
            ->where('banksline.datemovement','>=' ,$fromDate)
            ->where('banksline.datemovement','<=' ,$toDate)
            ->whereNotNull('campaigns.name_camp')
            ->groupBy('enterprise.name','projects.name'
                ,'city.city_name'
            )
            ->groupBy('campaigns.name_camp')


            ->orderBy('enterprise.name')
            ->orderBy('projects.name')
            ->orderBy('city.city_name')
        ;

        if($type==1){
            // רק חובה
            $result->where('banksline.amountmandatory',"!=" ,0);
            $result->whereNotNull('expense.name');
        }elseif ($type==2){
            //רק זכות
            $result->where('banksline.amountright',"!=" ,0);
            $result->whereNotNull('income.name');
        }

        $result = $result->get();

        return $result;
    }
}
