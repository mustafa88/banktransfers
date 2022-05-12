<?php

namespace App\Http\Controllers\bank;

use App\Http\Controllers\Controller;
use App\Http\Requests\bank\BankslineRequset;
use App\Models\bank\Banks;
use App\Models\bank\Banksdetail;
use App\Models\bank\Banksline;
use App\Models\bank\Enterprise;
use App\Models\Bank\Title_one;
use App\Traits\BankslineTrait;
use Illuminate\Http\Request;

class BankslineController extends Controller
{
    use BankslineTrait;
    public function showTable(Request $request ,$id_bank )
    {
        /*
         * ,$fromDate=null ,$toDate=null
        if($fromDate!=null and $toDate!=null){
            $request->session()->put('showLineBankFromDate',$fromDate);
            $request->session()->put('showLineBankToDate',$toDate);
        }
        **/
        if($request->fromDate!=null and $request->toDate!=null){
            $request->session()->put('showLineBankFromDate',$request->fromDate);
            $request->session()->put('showLineBankToDate',$request->toDate);
        }

        if($request->showTitleTwo!=null){
            $request->session()->put('showLineBankTitleTwo',$request->showTitleTwo);
        }

        if(!$request->session()->has('showLineBankFromDate')){
            $request->session()->put('showLineBankFromDate',date('Y-01-01'));
        }

        if(!$request->session()->has('showLineBankToDate')){
            $request->session()->put('showLineBankToDate',date('Y-12-31'));
        }
        if(!$request->session()->has('showLineBankTitleTwo')){
            $request->session()->put('showLineBankTitleTwo','0');
        }

        $showLineBankFromDate = $request->session()->get('showLineBankFromDate');
        $showLineBankToDate = $request->session()->get('showLineBankToDate');
        $showLineBankTitleTwo = $request->session()->get('showLineBankTitleTwo');
        //echo $showLineBankFromDate . " - " .$showLineBankToDate;

        $banksline = Banksline::with(['banks', 'titletwo', 'enterprise'])
            ->where('id_bank', '=', $id_bank)
            //->where('datemovement', '>=', '2022-01-01')
            //->where('datemovement', '<=', '2022-12-31')
            ->where('datemovement', '>=', $showLineBankFromDate)
            ->where('datemovement', '<=', $showLineBankToDate);
        if($showLineBankTitleTwo!='0'){
            $banksline->where('id_titletwo', '=', $showLineBankTitleTwo);
        }
        $banksline =   $banksline->get();
        //return $banksline;
        $bank = banks::find($id_bank);
        //return $bank;
        $enterprise = Enterprise::get();
        $title = Title_one::with(['titleTwo'])->get()->toArray();

        return view('manageabnk.linebanks', compact('banksline', 'bank', 'enterprise', 'title'))
            ->with(
                [
                    'pageTitle' => "תנועות חשבון בבנק",
                    'subTitle' => 'פירוט תנועות חשבון בבנק',
                ]
            );
    }


    public function editAjax($id_bank, $id_line)
    {

        $daterow = Banksline::with(['banks', 'titletwo', 'enterprise'])->where('id_bank', '=', $id_bank)->find($id_line);
        if (!$daterow) {
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'תקלה - שורה לא קיימת';
            return response()->json($resultArr);
        }
        $resultArr['status'] = true;
        $resultArr['cls'] = 'info';
        $resultArr['msg'] = 'עריכת שורה';
        $resultArr['row'] = $daterow;
        return response()->json($resultArr);
    }

    public function storeAjax(BankslineRequset $requset, $id_bank)
    {

        if ($requset->amountmandatory == 0 and $requset->amountright == 0) {
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'לא ניתן לשמור סכום חובה וזכות שווים לאפס';
            return response()->json($resultArr);
        }

        $duplicate = $this->checkIfDuplicate($id_bank, 0, $requset->datemovement, $requset->asmcta, $requset->amountmandatory, $requset->amountright);

        $arrDate = [
            'id_bank' => $id_bank,
            'datemovement' => $requset->datemovement,//תארך תנועה
            'datevalue' => $requset->datemovement,//תאריך ערך
            'description' => $requset->description,
            'note' => $requset->note,
            'asmcta' => $requset->asmcta,
            'amountmandatory' => $requset->amountmandatory,
            'amountright' => $requset->amountright,
            'id_titletwo' => $requset->id_titletwo,
            'id_enter' => $requset->id_enter,
            'duplicate' => $duplicate,
            'done' => 0,
        ];

        $rowinsert = Banksline::create($arrDate);

        $this->checkFixLine($rowinsert->id_line);

        $daterow = Banksline::with(['banks', 'titletwo', 'enterprise'])->where('id_bank', '=', $id_bank)->find($rowinsert->id_line);
        //::where('color_id', '=', $color_id)->first();
        //return redirect()->action([BanksController::class ,'showTable']);
        //return redirect()->back()->with("success", "تم الحفظ بنجاح");
        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['msg'] = 'تم الحفظ بنجاح';
        $resultArr['row'] = $daterow;
        return response()->json($resultArr);
    }

    /**
     * @param $id_bank מספר בנק
     * @param $id_line מספר שורה
     * @param $datemovement תאריך תנועה
     * @param $asmcta אסמכתא
     * @param $amountmandatory חובה
     * @param $amountright זכות
     * @return int =1 אם השורה כפול
     */
    public function checkIfDuplicate($id_bank, $id_line, $datemovement, $asmcta, $amountmandatory, $amountright)
    {
        //בדיקת אם השורה כפול

        $duplicate = 0;

        $chekDulpict = Banksline
            ::where('id_bank', $id_bank)
            ->where('id_line', "!=", $id_line)
            ->where('datemovement', $datemovement)
            ->where('asmcta', $asmcta)
            ->where('amountmandatory', $amountmandatory)
            ->where('amountright', $amountright)->first();

        if ($chekDulpict) {
            //חדש לשורה כפולה
            $duplicate = 1;
        }
        return $duplicate;
    }

    public function updateAjax(BankslineRequset $requset, $id_bank, $id_line)
    {

        $daterow = Banksline::with(['banks', 'titletwo', 'enterprise'])->where('id_bank', '=', $id_bank)->find($id_line);
        if (!$daterow) {
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'תקלה - שורה לא קיימת';
            return response()->json($resultArr);
        }
        $duplicate = $this->checkIfDuplicate($id_bank, $id_line, $requset->datemovement, $requset->asmcta, $requset->amountmandatory, $requset->amountright);

        $done = 0;
        /**
         * צריך להמשיך ולבדוק את הערך done - אם שווה ל1
         * לפי השורות של הפקודה לאחר השלמת התוכנה
         */

        //המשך עדכון ...................

        $daterow->datemovement = $requset->datemovement;
        $daterow->datevalue = $requset->datemovement;
        $daterow->description = $requset->description;
        $daterow->note = $requset->note;
        $daterow->asmcta = $requset->asmcta;
        $daterow->amountmandatory = $requset->amountmandatory;
        $daterow->amountright = $requset->amountright;
        $daterow->id_titletwo = $requset->id_titletwo;
        $daterow->id_enter = $requset->id_enter;
        $daterow->duplicate = $duplicate;
        $daterow->done = $done;

        $daterow->save();

        $daterow = Banksline::with(['banks', 'titletwo', 'enterprise'])->where('id_bank', '=', $id_bank)->find($id_line);
        $this->checkFixLine($id_line);
        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['msg'] = 'تم التعديل بنجاح';
        $resultArr['row'] = $daterow;
        return response()->json($resultArr);

    }

    public function deleteAjax($id_bank, $id_line)
    {
        $daterow = Banksline::with(['banks', 'titletwo', 'enterprise'])->where('id_bank', '=', $id_bank)->find($id_line);
        if (!$daterow) {
            $resultArr['status'] = false;
            $resultArr['cls'] = 'error';
            $resultArr['msg'] = 'תקלה - שורה לא קיימת';
            return response()->json($resultArr);
        }

        Banksdetail::where('id_line','=',$id_line)->delete();
        //$bankslin->delete();

        $daterow->delete();
        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['msg'] = 'تم الحذف بنجاح';
        //$resultArr['row'] =$daterow;
        return response()->json($resultArr);

    }


    public function  storeFileCsv(Request $requset, $id_bank)
    {

        //ddd($requset->filecsv);
        //https://medium.com/technology-hits/how-to-import-a-csv-excel-file-in-laravel-d50f93b98aa4

        $file = $requset->file('filecsv');
        if (!$file) {
            return redirect()->back()->with("success", "لم تتم قرائه الملف");
        }
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize(); //Get size of uploaded file in bytes
        //Check for file extension and size
        $this->checkUploadedFileProperties($extension, $fileSize);
        //var_dump($filename,$extension,$tempPath,$fileSize);
        $datacsv = array();
        $handle = fopen($tempPath, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $datacsv[] = $data;
        }
        fclose($handle);
        //$datacsv[1][1] =  iconv($in_charset = 'windows-1255' , $out_charset = 'UTF-8' , $datacsv[1][1]);
        //ddd($datacsv[1]);
        $counter = 0;
        $firstRow = 0;
        foreach ($datacsv as $item) {
            //exit;
            if ($firstRow == 0) {
                //לדלג על שורה ראשונה
                $firstRow = 1;
                continue;
            }
            $datemovement = $item[0];
            //$description = iconv($in_charset = 'windows-1255', $out_charset = 'UTF-8', $item[1]);
            $description = $item[1];
            $asmcta = $item[2];
            $amountmandatory = filter_var($item[3], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $amountright = filter_var($item[4], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if (empty($amountmandatory) or $amountmandatory == '') {
                $amountmandatory = 0;
            }
            if (empty($amountright) or $amountright == '' ) {
                $amountright = 0;
            }

            //$dateformat = \DateTime::createFromFormat('d/m/y', $datemovement);
            if(substr($datemovement,2,1)=='.'){
                if( strlen($datemovement)==10){
                    $dateformat = \DateTime::createFromFormat('d.m.Y', $datemovement);
                }elseif (strlen($datemovement)==8){
                    $dateformat = \DateTime::createFromFormat('d.m.y', $datemovement);
                }else{
                    ddd('date format - error');
                }
            }elseif (substr($datemovement,2,1)=='/' ){
                if( strlen($datemovement)==10){
                    $dateformat = \DateTime::createFromFormat('d/m/Y', $datemovement);
                }elseif (strlen($datemovement)==8){
                    $dateformat = \DateTime::createFromFormat('d/m/y', $datemovement);
                }else{
                    ddd('date format - error');
                }
            }elseif (substr($datemovement,4,1)=='-' ){
                if( strlen($datemovement)==10){
                    $dateformat = \DateTime::createFromFormat('Y-m-d', $datemovement);
                }elseif (strlen($datemovement)==8){
                    $dateformat = \DateTime::createFromFormat('y-m-d', $datemovement);
                }else{
                    ddd('date format - error');
                }
            }else{
                ddd('date format - error');
            }

            $datemovement = $dateformat->format('Y-m-d');

            $duplicate = $this->checkIfDuplicate($id_bank, 0, $datemovement, $asmcta, $amountmandatory, $amountright);
            //$duplicate = 0;
            $arrDate = [
                'id_bank' => $id_bank,
                'datemovement' => $datemovement,//תארך תנועה
                'datevalue' => $datemovement,//תאריך ערך
                'description' => $description,
                'asmcta' => $asmcta,
                'amountmandatory' => $amountmandatory,
                'amountright' => $amountright,
                'duplicate' => $duplicate,
                'done' => 0,
            ];
            //return $arrDate;
            Banksline::create($arrDate);
            $counter++;
        }
        return redirect()->back()->with("success", "تم الحفظ بنجاح {$counter} سطر جديد");

    }

    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 2097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($extension), $valid_extension) and $fileSize <= $maxFileSize) {
            return true;
        } else {
            throw new \Exception('Invalid file extension', response()::HTTP_UNSUPPORTED_MEDIA_TYPE); //415 error
        }
    }


    public function storeSelect(Request $requset, $id_bank)
    {
        $selectbox = $requset->selectbox;
        if(isset($requset->btn_savetitle)){
            //עדכון גורף לסוג תנועה
            Banksline::where('id_bank', '=', $id_bank)
                ->whereIn('id_line', $selectbox)
                ->update(['id_titletwo' => $requset->idselect_titletwo]);
        }elseif ($requset->btn_saveenter){
            //עדכון גורף לעמותה
            Banksline::where('id_bank', '=', $id_bank)
                ->whereIn('id_line', $selectbox)
                ->update(['id_enter' => $requset->idselect_enter]);
        }else{
            //echo $selectbox[0];
            foreach ($selectbox as $v_id_line){
                $this->storeDivDetail($v_id_line);
            }
            //ddd($selectbox);
        }
        $countLine = count($selectbox);
        return redirect()->back()->with("successupdateselect", "تم تعديل على {$countLine} سطر");
        //->header('Cache-Control', 'no-store, no-cache, must-revalidate')
    }



    /**
     * @param $id_line
     * מצביע חלוקה שווה לשורה בין כל הפרויקטים המשתתפים
     */
    public function storeDivDetail($id_line)
    {
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
        ])->find($id_line);

        if($bankslin['id_titletwo']=='2' ){
            //הוצאה מסוג תשלום לספק חייב  שיבחר שם ספק מרשימה לא ניתן שיהיה ריק
            ddd('error id_titletwo!=2');
        }

        $project = $bankslin['enterprise']['project'];

        $countDiv=0; //כמות פרויקטים וערים משתתפים או שייכים
        $projectCity = array();
        foreach ($project as $item){
            //$projectCity[$item['id']]['name']=$item['name'];
            $projectCity[$item['id']]=array();
            foreach ($item['city'] as $item2){
                $projectCity[$item['id']][$item2['city_id']]='x';
                $countDiv++;
            }
        }
        $scumline = $bankslin['amountmandatory'] + $bankslin['amountright'];

        if($countDiv==0 or $scumline==0){
            return false;
        }
        $sumDiv = round($scumline/$countDiv,2);

        //שארית חלוקה - מקבל פרויקט אחרון
        $sumDivMod = round($scumline - ($sumDiv * ($countDiv-1)),2);

        foreach ($projectCity as $id_proj => $city){
            foreach ($city as $id_city => $value){
                $projectCity[$id_proj][$id_city]=$sumDiv;
            }
        }

        foreach ($projectCity as $id_proj => $city){
            foreach ($city as $id_city => $value){
                $projectCity[$id_proj][$id_city]=$sumDivMod;
                break;
            }
            break;
        }
        //ddd($projectCity);
        $datapost = 'המשךךךךךךך';



        /**
        $sumInput = 0;
        foreach ($datapost as $key => $value){
            if(substr($key,0,4)=='dcom'){
                $sumInput += $value;
            }
        }
        if(round($sumInput - ($bankslin['amountmandatory'] + $bankslin['amountright']),2)!=0){
            //$x = round($sumInput - ($bankslin['amountmandatory'] + $bankslin['amountright']),2);
            return redirect()->back()->with("success", "שגיאה - סך הכל חלוקה לא שווה לסכום השורה");
        }
        **/

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

        foreach ($projectCity as $id_proj => $city){
            foreach ($city as $id_city => $value){
                $arrDate['id_proj']=$id_proj;
                $arrDate['id_city']=$id_city;
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
        return true;

    }

    /**
     * @param $id_bank
     * @param $id_line
     * @return \Illuminate\Http\JsonResponse
     * מחזיר שורה HTML לחלוקת פירוט שטרה ראשית
     */
    public function showrowdetilshtml($id_bank, $id_line)
    {
        //$id_line = 747;
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
        $fullscome = '1';
        $resultArr['status'] = true;
        $resultArr['cls'] = 'success';
        $resultArr['clss'] = $id_bank;
        $resultArr['clssg'] = $id_line;
        $resultArr['html'] = view('layout.includes.linedetailedit',compact('bankslin','fullscome'))->render();
        return response()->json($resultArr);

    }
}
