@extends('layout.mainangle')


@section('page-head')
    <style>
        .listol li {
            margin-bottom: 10px;
        }
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
                        <label for="bankid">בנק</label>
                        <select name="bankid" id="bankid" class="custom-select custom-select-sm">
                            <option value="0">בחר</option>
                            @foreach($banks as $item)
                                <option value="{{$item['id_bank']}}" @if(request()->bankid ==$item['id_bank']) selected @endif>{{$item['id_bank']}} - {{$item['enterprise']['name']}} </option>
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
                            <th>מס שורות</th>
                            <th>חובה</th>
                            <th>זכות</th>
                            <th>נטו</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($r1 as $item)
                            <tr>
                                <td>{{ $item['enterprise']['name'] }}</td>
                                <td>{{ $item['count_row'] }}</td>
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
                                <th>חודש</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r2 as $item)
                                <tr>
                                    <td>{{ $item['enterprise']['name'] }}</td>
                                    <td>{{ $item['month_year'] }}</td>
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
                                <th>סוג תנועה</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r3 as $item)
                                <tr>
                                    <td>{{ $item['enterprise']['name'] }}</td>
                                    <td>{{ $item['titletwo']['ttwo_text'] }}</td>
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

    @if(isset($r4))
        <div class="row">
            <div class="card card-default">
                <div class="card-header">Bordered Table</div>
                <div class="card-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>עמותה</th>
                                <th>סוג תנועה</th>
                                <th>חודש</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r4 as $item)
                                <tr>
                                    <td>{{ $item['enterprise']['name'] }}</td>
                                    <td>{{ $item['titletwo']['ttwo_text'] }}</td>
                                    <td>{{ $item['month_year'] }}</td>
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

    @if(isset($r5))
        <div class="row">
            <div class="card card-default">
                <div class="card-header">Bordered Table</div>
                <div class="card-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>עמותה</th>
                                <th>סוג תנועה</th>
                                <th>פרויקט</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r5 as $item)
                                <tr>
                                    <td>{{ $item->enterp }}</td>
                                    <td>{{ $item->ttwo_text }}</td>
                                    <td>{{ $item->proj }}</td>
                                    <td>{{ number_format($item->amountmandatory,2) }}</td>
                                    <td>{{ number_format($item->amountright,2) }}</td>
                                    <td>{{ number_format($item->total_neto,2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($r6))
        <div class="row">
            <div class="card card-default">
                <div class="card-header">Bordered Table</div>
                <div class="card-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>עמותה</th>
                                <th>סוג תנועה</th>
                                <th>פרויקט</th>
                                <th>עיר</th>
                                <th>חובה</th>
                                <th>זכות</th>
                                <th>נטו</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($r6 as $item)
                                <tr>
                                    <td>{{ $item->enterp }}</td>
                                    <td>{{ $item->ttwo_text }}</td>
                                    <td>{{ $item->proj }}</td>
                                    <td>{{ $item->city_name }}</td>
                                    <td>{{ number_format($item->amountmandatory,2) }}</td>
                                    <td>{{ number_format($item->amountright,2) }}</td>
                                    <td>{{ number_format($item->total_neto,2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection


@section('page-script')
    {{--  load file js from folder public --}}
@endsection

{{-- @include( "scripts.managetable.enterprise" ) --}}



