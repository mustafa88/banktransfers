@extends('layout.mainangle')


@section('page-head')
    <style>

    </style>
@endsection

@section('page-content')




    <div class="card card-default">
        <div class="card-header">انشاء تقرير بنكي</div>
        <div class="card-body">
            <div class="row">
                @if($errors->any())
                    {!! implode('', $errors->all('<div>:message</div>')) !!}
                @endif
                @if (Session::has('success'))
                    <div class="row">
                        <div class="alert alert-success" role="alert"><strong>{{ Session::get('success') }}</strong>
                        </div>
                    </div>
                @endif
            </div>
            <form>
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
                        <label for="city">עיר</label>
                        <select name="city" id="city" class="custom-select custom-select-sm">
                            <option value="0">الكل</option>
                            @foreach($city as $key1 => $item)
                                <option value="{{$item['city_id']}}"
                                        @if(request()->city ==($item['city_id']) )selected @endif>{{$item['city_name']}}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-auto">
                        <label for="fdate">מתאריך</label>
                        <input type="date"  name="fdate" id="fdate" value="{{request()->fdate}}" class="form-control mb-2" >
                    </div>

                    <div class="col-auto">
                        <label for="tdate">עד תאריך</label>
                        <input type="date"  name="tdate" id="tdate" value="{{request()->tdate}}" class="form-control mb-2" >
                    </div>

                    <div class="col-auto"><button class="btn btn-primary mb-2" type="submit">Submit</button></div>

                </div>
            </form>
        </div>
    </div>

    @if(isset($r1))
        <div class="row">
            <div class="card card-default">
                <div class="card-header">Bordered Table</div>
                <div class="card-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>עמותה</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r1 as $item)
                                <tr>
                                    <td>{{ $item['enterp'] }}</td>
                                    <td>{{ number_format($item['amountmandatory'],2) }}</td>
                                    <td>{{ number_format($item['amountright'],2) }}</td>
                                    <td>{{ number_format($item['total_neto'],2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($r2))
        <div class="row">
            <div class="card card-default">
                <div class="card-header">Bordered Table</div>
                <div class="card-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>עמותה</th>
                                <th>פרויקט</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r2 as $item)
                                <tr>
                                    <td>{{ $item['enterp'] }}</td>
                                    <td>{{ $item['proj'] }}</td>
                                    <td>{{ number_format($item['amountmandatory'],2) }}</td>
                                    <td>{{ number_format($item['amountright'],2) }}</td>
                                    <td>{{ number_format($item['total_neto'],2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($r3))
        <div class="row">
            <div class="card card-default">
                <div class="card-header">Bordered Table</div>
                <div class="card-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>עמותה</th>
                                <th>פרויקט</th>
                                <th>עיר</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r3 as $item)
                                <tr>
                                    <td>{{ $item['enterp'] }}</td>
                                    <td>{{ $item['proj'] }}</td>
                                    <td>{{ $item['city_name'] }}</td>
                                    <td>{{ number_format($item['amountmandatory'],2) }}</td>
                                    <td>{{ number_format($item['amountright'],2) }}</td>
                                    <td>{{ number_format($item['total_neto'],2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="card" role="tabpanel">
        <!-- Nav tabs-->
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link active" href="#moneyin" aria-controls="moneyin" role="tab" data-toggle="tab" aria-selected="true">הכנסה</a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" href="#moneyout" aria-controls="moneyout" role="tab" data-toggle="tab" aria-selected="false">הוצאה</a></li>
        </ul><!-- Tab panes-->
        <div class="tab-content p-0">
            <div class="tab-pane active" id="moneyin" role="tabpanel">
                <!-- START moneyin-->
                @if(isset($r4_in))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>תנועה</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r4_in as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['ttwo_text'] }}</td>
                                                <td>{{ number_format($item['amountright'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r5_in))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>תנועה</th>
                                            <th>סוג זכות</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r5_in as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['ttwo_text'] }}</td>
                                                <td>{{ $item['incomename'] }}</td>
                                                <td>{{ number_format($item['amountright'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r6_in))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>תנועה</th>
                                            <th>סוג זכות</th>
                                            <th>חודש</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r6_in as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['ttwo_text'] }}</td>
                                                <td>{{ $item['incomename'] }}</td>
                                                <td>{{ $item['month_year'] }}</td>
                                                <td>{{ number_format($item['amountright'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r7_in))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>קמפיין</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r7_in as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['name_camp'] }}</td>
                                                <td>{{ number_format($item['amountright'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="tab-pane" id="moneyout" role="tabpanel">
                <!-- START moneyout-->
                @if(isset($r4_out))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>תנועה</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r4_out as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['ttwo_text'] }}</td>
                                                <td>{{ number_format($item['amountmandatory'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r5_out))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>תנועה</th>
                                            <th>סוג חובה</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r5_out as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['ttwo_text'] }}</td>
                                                <td>{{ $item['expensename'] }}</td>
                                                <td>{{ number_format($item['amountmandatory'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r6_out))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>תנועה</th>
                                            <th>סוג חובה</th>
                                            <th>חודש</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r6_out as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['ttwo_text'] }}</td>
                                                <td>{{ $item['expensename'] }}</td>
                                                <td>{{ $item['month_year'] }}</td>
                                                <td>{{ number_format($item['amountmandatory'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r7_out))
                    <div class="row">
                        <div class="card card-default">
                            <div class="card-header">Bordered Table</div>
                            <div class="card-body">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>עמותה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>קמפיין</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r7_out as $item)
                                            <tr>
                                                <td>{{ $item['enterp'] }}</td>
                                                <td>{{ $item['proj'] }}</td>
                                                <td>{{ $item['city_name'] }}</td>
                                                <td>{{ $item['name_camp'] }}</td>
                                                <td>{{ number_format($item['amountmandatory'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>




@endsection


@section('page-script')
    {{--  load file js from folder public --}}
@endsection

{{-- @include( "scripts.managetable.enterprise" ) --}}



