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
                                <option value="{{$item['id_bank']}}" @if(request()->bankid ==$item['id_bank']) selected @endif>
                                    {{$item['id_bank']}} - {{$item['enterprise']['name']}}
                                        @if(isset($item['projects']['name']))
                                        - {{$item['projects']['name']}}
                                        @endif
                                </option>
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

    @if(isset($r7))
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
                            @foreach($r7 as $item)
                                <tr>
                                    <td>{{ $item->enterp }}</td>
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

    @if(isset($r8))
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
                            @foreach($r8 as $item)
                                <tr>
                                    <td>{{ $item->enterp }}</td>
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

    <div class="card" role="tabpanel">
        <!-- Nav tabs-->
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link active" href="#moneyin" aria-controls="moneyin" role="tab" data-toggle="tab" aria-selected="true">הכנסה</a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" href="#moneyout" aria-controls="moneyout" role="tab" data-toggle="tab" aria-selected="false">הוצאה</a></li>
        </ul><!-- Tab panes-->
        <div class="tab-content p-0">
            <div class="tab-pane active" id="moneyin" role="tabpanel">
                <!-- START moneyin-->
                @if(isset($r2_in))
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
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r2_in as $item)
                                            <tr>
                                                <td>{{ $item['enterprise']['name'] }}</td>
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

                @if(isset($r3_in))
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
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r3_in as $item)
                                            <tr>
                                                <td>{{ $item['enterprise']['name'] }}</td>
                                                <td>{{ $item['titletwo']['ttwo_text'] }}</td>
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
                                            <th>סוג תנועה</th>
                                            <th>חודש</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r4_in as $item)
                                            <tr>
                                                <td>{{ $item['enterprise']['name'] }}</td>
                                                <td>{{ $item['titletwo']['ttwo_text'] }}</td>
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
                                            <th>סוג תנועה</th>
                                            <th>פרויקט</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r5_in as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ number_format($item->amountright,2) }}</td>
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
                                            <th>סוג תנועה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r6_in as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ number_format($item->amountright,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r9_in))
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
                                            <th>סוג</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r9_in as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ $item->incomename }}</td>
                                                <td>{{ number_format($item->amountright,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r10_in))
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
                                            <th>סוג</th>
                                            <th>חודש</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r10_in as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ $item->incomename }}</td>
                                                <td>{{ $item->month_year }}</td>
                                                <td>{{ number_format($item->amountright,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                @if(isset($r11_in))
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
                                            <th>קמפיין</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r11_in as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->name_camp }}</td>
                                                <td>{{ number_format($item->amountright,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r12_in))
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
                                            <th>חודש</th>
                                            <th>קמפיין</th>
                                            <th>זכות</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r12_in as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->month_year }}</td>
                                                <td>{{ $item->name_camp }}</td>
                                                <td>{{ number_format($item->amountright,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r13_in))
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
                                        @foreach($r13_in as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ $item->name_camp }}</td>
                                                <td>{{ number_format($item->amountright,2) }}</td>
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
                @if(isset($r2_out))
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
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r2_out as $item)
                                            <tr>
                                                <td>{{ $item['enterprise']['name'] }}</td>
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

                @if(isset($r3_out))
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
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r3_out as $item)
                                            <tr>
                                                <td>{{ $item['enterprise']['name'] }}</td>
                                                <td>{{ $item['titletwo']['ttwo_text'] }}</td>
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
                                            <th>סוג תנועה</th>
                                            <th>חודש</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r4_out as $item)
                                            <tr>
                                                <td>{{ $item['enterprise']['name'] }}</td>
                                                <td>{{ $item['titletwo']['ttwo_text'] }}</td>
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
                                            <th>סוג תנועה</th>
                                            <th>פרויקט</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r5_out as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ number_format($item->amountmandatory,2) }}</td>
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
                                            <th>סוג תנועה</th>
                                            <th>פרויקט</th>
                                            <th>עיר</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r6_out as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ number_format($item->amountmandatory,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r9_out))
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
                                            <th>ספק</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r9_out as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ $item->expensename }}</td>
                                                <td>{{ number_format($item->amountmandatory,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r10_out))
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
                                            <th>ספק</th>
                                            <th>חודש</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r10_out as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->ttwo_text }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ $item->expensename }}</td>
                                                <td>{{ $item->month_year }}</td>
                                                <td>{{ number_format($item->amountmandatory,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r11_out))
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
                                            <th>קמפיין</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r11_out as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->name_camp }}</td>
                                                <td>{{ number_format($item->amountmandatory,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($r12_out))
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
                                            <th>חודש</th>
                                            <th>קמפיין</th>
                                            <th>חובה</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($r12_out as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->month_year }}</td>
                                                <td>{{ $item->name_camp }}</td>
                                                <td>{{ number_format($item->amountmandatory,2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                @if(isset($r13_out))
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
                                        @foreach($r13_out as $item)
                                            <tr>
                                                <td>{{ $item->enterp }}</td>
                                                <td>{{ $item->proj }}</td>
                                                <td>{{ $item->city_name }}</td>
                                                <td>{{ $item->name_camp }}</td>
                                                <td>{{ number_format($item->amountmandatory,2) }}</td>
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



