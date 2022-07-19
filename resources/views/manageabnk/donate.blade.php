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
        <div class="card-header">
            <h4 class="card-title">
                <a class="text-inherit" data-toggle="collapse" href="#addline" aria-expanded="true">
                    <small><em class="fa fa-plus text-primary mr-2"></em></small>
                    <span>הוספה/עריכה תנועה בנק</span>
                </a>
            </h4>

        </div>
        <div class="card-body collapse" id="addline">
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
                        <label for="enterp">مؤسسة / مشروع</label>
                        <select name="enterp" id="enterp" class="custom-select custom-select-sm">
                            <option value="0">בחר</option>
                            @foreach($enterprise as $key1 => $item)
                                <option value="{{$item['id']}}*0"
                                        @if(request()->enterp ==($item['id']."*0")) selected @endif>{{$key1+1}}) {{$item['name']}}</option>
                                @foreach($item['project'] as $key2 => $item2)
                                    <option value="{{$item['id']}}*{{$item2['id']}}"
                                            @if(request()->enterp ==($item['id']."*".$item2['id']) )selected @endif>*{{$item2['name']}}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>


                    <div class="col-auto">
                            <label for="nobank">שורה לא בבנק</label>
                            <input type="checkbox" name='nobank' id="nobank"  value="1"
                                   class="form-control" @if(isset($bankedt) and $bankedt['nobank']=='1') checked @endif
                            >
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

        <form method="post" action="#" id="formselct">
        @csrf

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
                        <button class="mb-2 btn btn-success" type="button" id="showbydate">عرض</button>
                    </div>
                </div>


            </div>
            <div class="card-body">
                <table class="table table-striped my-4 w-100 hover" id="datatable1">
                    <thead>
                    <tr>
                        <th>תאריך תנועה</th>
                        <th>תיאור</th>
                        <th>אסמכתא</th>
                        <th>חובה</th>
                        <th>זכות</th>
                        <th>שורה בבנק</th>
                        <th>סוג תנועה</th>
                        <th>עמותה</th>
                        <th>הערה</th>
                        <th>מצב שורה</th>
                        <th>פעולה</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--
                    @foreach($banksline as $item)
                        @include('layout.includes.linedetail_displayrowl',['rowBanksLine' => $item])
                    @endforeach
                    --}}
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

    @include( "scripts.managebank.donate" )

    @include('layout.includes.linedetailedit')

    @stack('linedetailedit-script')

@endsection





