@extends('layout.mainangle')


@section('page-head')
    <!-- Datatables-->
    <link rel="stylesheet" href="{{ asset('angle/vendor/datatables.net-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('angle/vendor/datatables.net-keytable-bs/css/keyTable.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('angle/vendor/datatables.net-responsive-bs/css/responsive.bootstrap.css') }}">
    <style>
        .dropmenu{
            position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 34px, 0px);
        }
    </style>
@endsection

@section('page-content')

    @if($errors->any())
        {!! implode('', $errors->all('<div>:message</div>')) !!}
    @endif
    <div class="card card-default">
        <div class="card-header">הוספה/עריכה תנועה בנק</div>
        <div class="card-body">
            <form method="post" name="myform" id="myform" action="#">
                @csrf
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label for="datemovement">תאריך תנועה</label>
                        <input class="form-control mb-2" name="datemovement" id="datemovement" type="date"
                               value="{{ $bankedt['datemovement'] ?? '' }}">
                    </div>


                    <div class="col-auto">
                        <label for="description">תיאור</label>
                        <input class="form-control mb-2" name='description' id="description" type="text"
                               value="{{ $bankedt['description'] ?? '' }}">
                    </div>

                    <div class="col-auto">
                        <label for="asmcta">אסמכתא</label>
                        <input class="form-control mb-2" name='asmcta' id="asmcta" type="number"
                               value="{{ $bankedt['asmcta'] ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <label for="amountmandatory">חובה</label>
                        <input class="form-control mb-2" name='amountmandatory' id="amountmandatory" type="number"
                               value="{{ $bankedt['amountmandatory'] ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <label for="amountright">זכות</label>
                        <input class="form-control mb-2" name='amountright' id="amountright" type="number"
                               value="{{ $bankedt['amountright'] ?? '' }}">
                    </div>

                </div>

                <div class="form-row align-items-center">

                    <div class="col-auto">


                        <label for="id_titletwo">סוג תנועה</label>
                        <select class="form-control mb-2 custom-select custom-select-sm" name="id_titletwo"
                                id="id_titletwo">
                            <option value="0">בחר</option>
                            @foreach($title as $item)
                                <optgroup label="{{$item['tone_text']}}">
                                    @if(isset($item['title_two']))
                                        @foreach ($item['title_two'] as $item2 )
                                            <option value="{{$item2['ttwo_id']}}"
                                                    @if(isset($bankedt['id_titletwo']) and $bankedt['id_titletwo'] =$item2['ttwo_id'] )selected @endif
                                            > {{$item2['ttwo_text']}}</option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="id_enter">עמותה</label>
                        <select class="form-control mb-2 custom-select custom-select-sm" name="id_enter" id="id_enter">
                            <option value="0">בחר</option>

                            @foreach($enterprise as $item)
                                <option value="{{$item['id']}}"
                                        @if($bank['id_enter']==$item['id'] )selected @endif
                                >{{$item['name']}}</option>
                            @endforeach

                        </select>
                    </div>


                    <div class="col-auto">
                        <label for="note">הערה</label>
                        <input class="form-control mb-2" name='note' id="note" type="text"
                               value="{{ $bankedt['note'] ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary mb-2" type="button" name="btn_save" id="btn_save">حفظ</button>
                    </div>
                </div>
                <input type="hidden" name="id_line" id="id_line" value="0">
            </form>

            <div>
                @if (Session::has('success'))
                    <div class="row">
                        <div class="alert alert-success" role="alert"><strong>{{ Session::get('success') }}</strong></div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="card card-default">
        <div class="card-header">צרף קובץ</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" action="{{route('linebanks.storeFileCsv',$bank['id_bank'])}}">
                @csrf
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <fieldset>
                            <div class="form-group row"><label class="col-md-4 col-form-label">בחר קובץ</label>
                                <div class="col-md-10">
                        <input type="file" name="filecsv" id="filecsv" accept=".csv" class="form-control filestyle" data-classbutton="btn btn-secondary" data-classinput="form-control inline" data-icon="&lt;span class='fa fa-upload mr-2'&gt;&lt;/span&gt;">
                                </div>
                                <button class="btn btn-primary mb-2" type="submit" name="btn_savecsv" >حفظ</button>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>
        <form method="post" action="{{route('linebanks.storeselecttitle',$bank['id_bank'])}}" id="formselct">
        @csrf

    <div class="card card-default">
        <div class="card-header">עדכון גורף לכל השורות המסומנות</div>
        <div class="card-body">

            @if (Session::has('successupdateselect'))
                <div class="row">
                    <div class="alert alert-success" role="alert"><strong>{{ Session::get('successupdateselect') }}</strong></div>
                </div>
            @endif

                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label for="id_titletwo">עדכון גורף ל - סוג תנועה</label>
                        <select class="form-control custom-select custom-select-sm " name="idselect_titletwo" id="idselect_titletwo"  >
                            <option value="0">בחר</option>
                            @foreach($title as $item)
                                <optgroup label="{{$item['tone_text']}}">
                                    @if(isset($item['title_two']))
                                        @foreach ($item['title_two'] as $item2 )
                                            <option value="{{$item2['ttwo_id']}}"> {{$item2['ttwo_text']}}</option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="submit" name="btn_savetitle" id="btn_savetitle" value="حفظ" class="btn btn-primary mb-2">
                    </div>

                    <div class="col-auto">
                        <label for="idselect_enter">עמותה</label>
                        <select class="form-control custom-select custom-select-sm " name="idselect_enter" id="idselect_enter">
                            <option value="0">בחר</option>
                            @foreach($enterprise as $item)
                                <option value="{{$item['id']}}" >{{$item['name']}}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-auto">
                        <input type="submit" name="btn_saveenter" id="btn_saveenter" value="حفظ" class="btn btn-primary mb-2">
                    </div>

                    <div class="col-auto">
                        <input type="submit" name="btn_save_divline_amlot" id="btn_save_divline_amlot" value="חלוקת עמלות בנק - בין כל הארגונים" class="btn btn-primary mb-2">
                    </div>
                </div>

        </div>
    </div>

    <div>

        <!-- DATATABLE DEMO 1-->
        <div class="card card-default">
            <div class="card-header">
                <div class="card-title">שורות בנק</div>

                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label  for="fromdate">מתאריך</label>
                        <input type="date"  name="fromdate" id="fromdate" value="{{session()->get('showLineBankFromDate')}}" class="form-control" >
                    </div>
                    <div class="col-auto">
                        <label for="todate" >עד תאריך</label>
                        <input type="date"  name="todate" id="todate" value="{{session()->get('showLineBankToDate')}}" class="form-control" >
                    </div>
                    <div class="col-auto">
                        <label for="showTitleTwo">סוג תנועה</label>
                        <select class="form-control custom-select custom-select-sm  " name="showTitleTwo" id="showTitleTwo"  >
                            <option value="0">הכל</option>
                            @foreach($title as $item)
                                <optgroup label="{{$item['tone_text']}}">
                                    @if(isset($item['title_two']))
                                        @foreach ($item['title_two'] as $item2 )
                                            <option value="{{$item2['ttwo_id']}}"
                                           @if(session()->get('showLineBankTitleTwo')==$item2['ttwo_id']) selected @endif> {{$item2['ttwo_text']}}</option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="mb-2 btn btn-success" type="button" id="showbydate">عرض</button>
                    </div>
                </div>

                <div class="text-sm">
                    <button class="mb-1 btn btn-outline-primary" type="button" onclick="selectAll()">סמן הכל</button>
                    <button class="mb-1 btn btn-outline-warning" type="button" onclick="unSelectAll()">בטל סימון</button>
                    <button class="mb-1 btn  btn-purple" type="button" onclick="divlineditels()">חלוקת שורות מסומנות</button>
                </div>

                <div class="row row-flush">
                    <div class="col-12 col-md-1">
                        <div class="table-success text-center">שורה תקינה</div>
                    </div>
                    <div class="col-12 col-md-1">
                        <div class="table-warning text-center">שורה לא תקינה</div>
                    </div>
                    <div class="col-12 col-md-1">
                        <div class="table-danger text-center">שורה כפולה</div>
                    </div>
                </div>




            </div>
            <div class="card-body">
                <table class="table table-striped my-4 w-100" id="datatable1">
                    <thead>
                    <tr>
                        <th>תאריך תנועה</th>
                        <th>תיאור</th>
                        <th>אסמכתא</th>
                        <th>חובה</th>
                        <th>זכות</th>
                        <th>סוג תנועה</th>
                        <th>עמותה</th>
                        <th>הערה</th>
                        <th>מצב שורה</th>
                        <th>פעולה</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($banksline as $item)
                        <tr>
                            <td>{{$item['datemovement']}}</td>
                            <td>{{$item['description']}}</td>
                            <td>{{$item['asmcta']}}</td>
                            <td>{{$item['amountmandatory']}}</td>
                            <td>{{$item['amountright']}}</td>
                            <td>
                                @if(!empty($item['titletwo']['ttwo_text']))
                                    {{$item['titletwo']['ttwo_text']}}
                                @endif
                            </td>
                            <td>
                                @if(!empty($item['enterprise']['name']))
                                    {{$item['enterprise']['name']}}
                                @endif
                            </td>
                            <td>{{$item['note']}}</td>
                            <td>
                                <span class="@if($item['duplicate']==1) table-danger  @elseif($item['done']==1) table-success @else table-warning @endif">
                                @if($item['duplicate']==1) שורה כפולה  @elseif($item['done']==1) שורה תקינה @else שורה לא תקינה @endif
                                </span>
                            </td>
                            <td>
                                <div class="btn-group mb-1">
                                    <button class="btn dropdown-toggle btn-primary" type="button" data-toggle="dropdown"
                                            aria-expanded="false">בחר
                                    </button>
                                    <div class="dropdown-menu dropmenu" role="menu" x-placement="bottom-start">
                                        <a class="dropdown-item edit_row" href="javascript:void(0)" data-idline="{{$item['id_line']}}"><i class="far fa-edit"></i> עריכה</a>
                                        <a class="dropdown-item delete_row" href="javascript:void(0)" data-idline="{{$item['id_line']}}"><i class="far fa-trash-alt"></i> מחיקה</a>
                                        @if($item['duplicate']==1)
                                            <a class="dropdown-item dulicate_row" href="javascript:void(0)" data-idline="{{$item['id_line']}}"><i class="far fa-check-circle"></i> אישור שורה</a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item detail_row" href="{{route('linedetail.show',$item['id_line'])}}" data-idline="{{$item['id_line']}}">עריכת פירוט שורה</a>
                                    </div>
                                </div>
                                <label class="c-checkbox">
                                    <input type="checkbox" name="selectbox[]" value="{{$item['id_line']}}"
                                           data-titletwo="{{$item['id_titletwo']}}" data-done="{{$item['done']}}"
                                    data-titletwo="{{$item['id_titletwo']}}">
                                    <span class="fa fa-check"></span>סמן שורה</label>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </form>
@endsection



@section('page-script')

    <!-- Datatables-->
    <script src="{{ asset('angle/vendor/datatables.net/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-buttons/js/dataTables.buttons.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-buttons-bs/js/buttons.bootstrap.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.colVis.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.flash.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.html5.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.print.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-keytable/js/dataTables.keyTable.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-responsive/js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('angle/vendor/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script src="{{ asset('angle/vendor/jszip/dist/jszip.js') }}"></script>
    <script src="{{ asset('angle/vendor/pdfmake/build/pdfmake.js') }}"></script>
    <script src="{{ asset('angle/vendor/pdfmake/build/vfs_fonts.js') }}"></script>
    <!-- FILESTYLE-->
    <script src="{{ asset('angle/vendor/bootstrap-filestyle/src/bootstrap-filestyle.js') }}"></script><!-- TAGS INPUT-->
    <script src="{{ asset('angle/vendor/sweetalert2/dist/sweetalert2.all.min.js') }}"></script><!-- SWEET ALERT-->

    @include( "scripts.managebank.linebanks" )

    @include('layout.includes.linedetailedit')

    @stack('linedetailedit-script')

@endsection





