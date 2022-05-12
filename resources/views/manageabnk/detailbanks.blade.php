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
        <div class="card-header">טסט</div>
        <div class="card-body">
            @if (Session::has('success'))
                <div class="row">
                    <div class="alert alert-success" role="alert"><strong>{{ Session::get('success') }}</strong></div>
                </div>
            @endif
            <div class="table-responsive col-xl-8">
            <table class="table table-striped my-4 w-100" >
                <thead>
                <tr>
                    <th>תאריך תנועה</th>
                    <th>תיאור</th>
                    <th>אסמכתא</th>
                    @if($bankslin['amountmandatory']!=0)<th>חובה</th>@endif
                    @if($bankslin['amountright']!=0)<th>זכות</th>@endif
                    <th>סוג תנועה</th>
                    <th>עמותה</th>
                    <th>הערה</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$bankslin['datemovement']}}</td>
                        <td>{{$bankslin['description']}}</td>
                        <td>{{$bankslin['asmcta']}}</td>
                        @if($bankslin['amountmandatory']!=0)<td id="sum_mandatory">{{$bankslin['amountmandatory']}}</td>@endif
                        @if($bankslin['amountright']!=0)<td id="sum_right">{{$bankslin['amountright']}}</td>@endif
                        <td>@if(!empty($bankslin['titletwo']['ttwo_text']))
                                {{$bankslin['titletwo']['ttwo_text']}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($bankslin['enterprise']['name']))
                                {{$bankslin['enterprise']['name']}}
                            @endif
                        </td>
                        <td>{{$bankslin['note']}}</td>
                    </tr>

                </tbody>
            </table>
        </div>
            <form method="post" name="myform" id="myform" action="#">
                @csrf


        <div class="table-responsive col-xl-12">
            <div class="alert alert-info " role="alert" id="msginfo">{{$msginfo}}</div>

            @include('layout.includes.linedetailedit' ,['bankslin' => $bankslin])

            </div>
                <input type="hidden" name="id_line" id="id_line" value="0">

                </form>

                @if($bankslin['id_titletwo']!='2')
                    <form method="post" name="myformdivline" id="myformdivline" action="{{route('linedetail.storedivline',$bankslin['id_line'])}}">
                        @csrf
                        @method('post')
                        <div class="col-xl-8">
                        <div class="alert alert-info col-xl-6" role="alert" id="msginfo2">{{$msginfo}}</div>

                        <button class="btn btn-primary mb-2" type="button" name="btn_div" id="btn_div"
                                data-scum="@if($bankslin['amountmandatory']!=0){{$bankslin['amountmandatory']}}@else{{$bankslin['amountright']}}@endif">חלוקת סכום</button>
                            <button class="btn btn-primary mb-2" type="button" name="btn_div_save" id="btn_div_save">שמור חלוקה</button>

                        </div>


                            <div class="table-responsive   table-bordered col-xl-8">

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>البلد/المشروع</th>
                                    @foreach($allProject as $item)
                                        <th> {{$item}} </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($allCity as $key1 => $item1)
                                    <tr>
                                    <th> {{$item1}} </th>

                                        @foreach($allProject as $key2 => $item2)
                                            <td> @if(isset($projectCity[$key2]['city'][$key1]))
                                                    <div class="col-auto">
                                                        <label class="sr-only" for="dcom*{{$key2}}*{{$key1}}">Name</label>
                                                        <input type="number" name="dcom*{{$key2}}*{{$key1}}" id="dcom*{{$key2}}*{{$key1}}" value="0" min="0" class="form-control mb-2 allinptscom" >
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach

                                    </tr>
                                @endforeach

                                </tbody>

                            </table>
                        </div>

                    </form>
                @endif

                <table class="table table-striped my-4 w-100" id="datatable1">
                    <thead>
                    <tr>
                        <th>
                            @if($bankslin['amountmandatory']=='0')
                                זכות
                            @else
                                חוב
                            @endif
                        </th>

                        <th>פרויקט</th>
                        <th>עיר</th>

                        <th>
                        @if($bankslin['amountmandatory']=='0')
                            סוג הכנסה
                        @else
                            סוג הוצאה
                        @endif
                       </th>
                        <th>קמפיין</th>
                        <th>הערה</th>
                        <th>פעולה</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($bankslin['banksdetail']  as $item)
                            <tr>
                                <td>
                                    @if($bankslin['amountmandatory']=='0')
                                        {{ $item['amountright'] }}
                                    @else
                                        {{ $item['amountmandatory'] }}
                                    @endif
                                </td>

                                <td>{{ $item['projects']['name'] }}</td>
                                <td>{{ $item['city']['city_name'] }}</td>

                                <td>
                                @if($bankslin['amountmandatory']=='0')
                                        @if(!empty($item['income']['name'])){{ $item['income']['name'] }}@endif
                                @else
                                        @if(!empty($item['expense']['name'])){{ $item['expense']['name']}}@endif
                                @endif
                                </td>

                                <td> @if(!empty($item['campaigns']['name_camp']))
                                        {{$item['campaigns']['name_camp']}}
                                    @endif
                                </td>

                                <td>{{ $item['note'] }}</td>

                                <td>
                                    <div class="btn-group mb-1">
                                        <button class="btn dropdown-toggle btn-primary" type="button" data-toggle="dropdown"
                                                aria-expanded="false">בחר
                                        </button>
                                        <div class="dropdown-menu dropmenu" role="menu" x-placement="bottom-start">
                                            <a class="dropdown-item edit_row" href="javascript:void(0)" data-idline="{{$item['id_detail']}}"><i class="far fa-edit"></i> עריכה</a>
                                            <a class="dropdown-item delete_row" href="javascript:void(0)" data-idline="{{$item['id_detail']}}"><i class="far fa-trash-alt"></i> מחיקה</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>


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

        @include( "scripts.managebank.detailbanks" )

        @stack('linedetailedit-script')
    @endsection





