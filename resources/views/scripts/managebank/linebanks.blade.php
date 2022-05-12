
    <script type="text/javascript">
        let myTable,myRowTable=null;
        $(document).ready(function(){

            myTable = $('#datatable1').DataTable({
                'paging': true, // Table pagination
                'ordering': true, // Column ordering
                "order": [[ 1, 'asc'],[ 0, 'asc']],
                'info': true, // Bottom left status text
                responsive: true,
                aLengthMenu: [
                    [25, 50, 100, 200, -1],
                    [25, 50, 100, 200, "All"]
                ],
                iDisplayLength: -1
            });

           // let rowNode = myTable.row.add( [ '1', 32, 'Edinburgh' ,'xxxx'] ).draw().node();
            $('#formselct').submit(function() {
                var name_submit = $("input[type=submit][clicked=true]").attr('name');
                if(name_submit=="btn_savetitle" && $("#idselect_titletwo").val()=="0"){
                    notify('يرجى اختيار قيمه','error');
                    return false;
                }

                if(name_submit=="btn_saveenter" && $("#idselect_enter").val()=="0"){
                    notify('يرجى اختيار قيمه','error');
                    return false;
                }

                if(name_submit=="btn_save_divline_amlot"){
                    //btn_save_divline_amlot
                    myTable.page.len( -1 ).search('').draw();
                    $('input[type="checkbox"]').prop("checked",false);
                    $('input[type="checkbox"][data-titletwo="1"][data-done="0"]').prop("checked",true);
                    //alert('aaa');
                    //return false;
                }


                //btn_save_divline_amlot
                myTable.page.len( -1 ).search('').draw();
                if($("input:checked[name='selectbox[]']").length===0){
                    notify('لم يتم اختيار اي سطر من الجدول','error');
                    return false;
                }

                return true;

            });

            $("#formselct input[type=submit]").click(function() {
                $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
                $(this).attr("clicked", "true");
            });

        });
        /**
        $(document).on('change', '#datemovement', function (e) {
            $("#datevalue").val($(this).val());
        });
         **/

        $(document).on('change', '#amountmandatory', function (e) {
            $("#amountright").val('0');
        });
        $(document).on('change', '#amountright', function (e) {
            $("#amountmandatory").val('0');
        });

        /**
         * שמירה
         * save new data or update data exists
         */
        $('#btn_save').on( 'click', function () {
            /**
            if (!$('#myform').parsley().validate() ) {
                return false;
            }
             **/
            //alert("aaa");
            let dropmenu=`<div class="btn-group mb-1">
                                    <button class="btn dropdown-toggle btn-primary" type="button" data-toggle="dropdown"
                                            aria-expanded="false">בחר
                                    </button>
                                    <div class="dropdown-menu dropmenu" role="menu" x-placement="bottom-start">
                                        <a class="dropdown-item edit_row" href="javascript:void(0)" data-idline="ID_LINE"><i class="far fa-edit"></i> עריכה</a>
                                        <a class="dropdown-item delete_row" href="javascript:void(0)" data-idline="ID_LINE"><i class="far fa-trash-alt"></i> מחיקה</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item detail_row" href="{{route('linedetail.show','ID_LINE')}}" data-idline="ID_LINE">עריכת פירוט שורה</a>
                                    </div>
                                </div>
                                <label class="c-checkbox"><input type="checkbox" name="selectbox[]" value="ID_LINE">
                                <span class="fa fa-check"></span>סמן שורה</label>`;
            let id_line = $("#id_line").val();

            if((id_line=='0' && myRowTable!=null) || (id_line!='0' && myRowTable==null)){
                notify('תקלה - נא לדווח לאיש מחשוב');
                return;
            }

            //alert(id_line);
            if(id_line=='0'){
                //insert
                //notify('insert');
                //return;

                let url= '{{ route('linebanks.storeajax',$bank['id_bank']) }}';
                let resultAjax = SendToAjax(url,'POST');
                //console.log(resultAjax);
                if(resultAjax==undefined){
                    notify('حدث خطأ','error');
                    return false;
                }
                notify(resultAjax.msg ,resultAjax.cls);
                if(resultAjax.status===false){
                    return;
                }
               //return;
                let newData = resultAjax['row'];
                /**
                let done='לא';
                if(newData.done==='1'){
                    done='כן';
                }
                **/

                let done='';
                if(newData.duplicate==='1'){
                    done=`<span class="table-danger">שורה כפולה</span>`;
                }else if(newData.done==='1'){
                    done=`<span class="table-success">שורה תקינה</span>`;
                }else{
                    done=`<span class="table-warning">שורה לא תקינה</span>`;
                }

                let id_line = newData.id_line;

                dropmenu = dropmenu.replaceAll("ID_LINE", id_line);
                //const name = 'Chris';
                //const greeting = `Hello, ${name}`;

                let rowNode = myTable.row.add( [
                    newData.datemovement,
                    newData.description,
                    newData.asmcta,
                    newData.amountmandatory,
                    newData.amountright,
                    newData.titletwo.ttwo_text,
                    newData.enterprise.name,
                    newData.note,
                    done,
                    dropmenu,
                ] ).draw().node();
                //$( rowNode ).find('td').eq(1).css("direction",'ltr').css("background-color",newData.color_code);
            }else{
                //return;
                //update
                //notify('update');
                {{--let url= '{{ route('color.update.ajax') }}';--}}
                let url= '{{ route('linebanks.updateajax',['id_bank' => $bank['id_bank']]) }}';
                //alert(url);alert(id_line);
                url +="/"+id_line;
                //alert(url); return;
                let resultAjax = SendToAjax(url,'PUT');
                //console.log(resultAjax);
                if(resultAjax==undefined){
                    notify('حدث خطأ','error');
                    return false;
                }
                notify(resultAjax.msg ,resultAjax.cls);
                if(resultAjax.status===false){
                    return;
                }
                let newData = resultAjax['row'];
                /**
                let done='לא';
                if(newData.done==='1'){
                    done='כן';
                }
                **/
                let done='';
                if(newData.duplicate==='1'){
                    done=`<span class="table-danger">שורה כפולה</span>`;
                }else if(newData.done==='1'){
                    done=`<span class="table-success">שורה תקינה</span>`;
                }else{
                    done=`<span class="table-warning">שורה לא תקינה</span>`;
                }

                let id_linerow = newData.id_line;

                dropmenu = dropmenu.replaceAll("ID_LINE", id_linerow);
                //'שינוי ערך שדה בטבלה'
                let numberRow = myTable.row(myRowTable)[0][0];
                console.log(newData);
                let row = myTable.row(numberRow);
                myTable.cell(row, 0).data(newData.datemovement);
                myTable.cell(row, 1).data(newData.description);
                myTable.cell(row, 2).data(newData.asmcta);
                myTable.cell(row, 3).data(newData.amountmandatory);
                myTable.cell(row, 4).data(newData.amountright);
                myTable.cell(row, 5).data(newData.titletwo.ttwo_text);
                myTable.cell(row, 6).data(newData.enterprise.name);
                myTable.cell(row, 7).data(newData.note);
                myTable.cell(row, 8).data(done);
                myTable.cell(row, 9).data(dropmenu);
                myTable.draw();

                //let thisRow = row.node();
                //$(thisRow).find('td').eq(1).css("background-color",newData['color_code']);


            }

            InitPage();
        });

        $(document).on('click', '#showbydate', function (e) {
            var fdate= $("#fromdate").val();
            var tdate= $("#todate").val();
            var showTitleTwo= $("#showTitleTwo").val();
            let url='{{ route('linebanks.show',['id_bank' => $bank['id_bank']]) }}';

            if(fdate=="" || tdate==""){
                notify("תאריך לא תקין" ,"error");
                return false;
            }
            //url += "/" + fdate + "/" + tdate;
            url += "?fromDate=" + fdate + "&toDate=" + tdate+ "&showTitleTwo=" + showTitleTwo;
            //alert(url);
            window.location = url;
        });

        $(document).on('click', 'a.edit_row', function (e) {
            e.preventDefault();
            InitPage();
            let idline = $(this).data('idline');
            //alert($(this).data('idline'));
            //return;
            var nRow = $(this).parents('tr')[0];
            var aData = myTable.row(nRow).data();

            //return;

            //['id' => $artist->id, 'name' => $artist->name]
            let url='{{ route('linebanks.editajax',['id_bank' => $bank['id_bank']]) }}';
            url +="/"+idline;
            //alert(url);

            let resultAjax = SendToAjax(url,'GET');
            //console.log(resultAjax);
            if(resultAjax.status===false){
                notify(resultAjax.msg ,resultAjax.cls);
                return;
            }
            let row = resultAjax.row;
            //console.log(row);
            $("#datemovement").val(row.datemovement);
            $("#description").val(row.description);
            $("#asmcta").val(row.asmcta);
            $("#amountmandatory").val(row.amountmandatory);
            $("#amountright").val(row.amountright);
            $("#id_titletwo").val(row.id_titletwo);
            $("#id_enter").val(row.id_enter);
            $("#note").val(row.note);

            myRowTable=nRow;
            $("#id_line").val(idline);

        });

        $(document).on('click', 'a.delete_row', function (e) {
            e.preventDefault();
            InitPage();
            var r = confirm("يرجى الموافقه على الحذف");
            if(r===false){
                return false;
            }
            var nRow = $(this).parents('tr')[0];
            var aData = myTable.row(nRow).data();
            let idline = $(this).data('idline');
            $("#id_line").val(idline);
            let url= '{{ route('linebanks.deleteajax',['id_bank' => $bank['id_bank']]) }}';
            url +="/"+idline;
            let resultAjax = SendToAjax(url,'DELETE');
            console.log(resultAjax);
            if(resultAjax==undefined){
                notify('حدث خطأ','error');
                return false;
            }
            notify(resultAjax.msg ,resultAjax.cls);
            if(resultAjax.status===false){
                return;
            }

            myTable.row( nRow) .remove().draw();
            InitPage();
        });



        $(document).on('click', 'a.dulicate_row', function (e) {
            e.preventDefault();
            var r = confirm("נא לאשר שהשורה לא כפולה");
            if(r===false){
                return false;
            }
            alert("צריך להמשיך פעולה....");
        });

        $(document).on('click', 'a.detail_row', function (e) {

        });

        function InitPage(){
            myRowTable=null;
            $("#id_line").val('0');

            $("#datemovement").val('');
            $("#description").val('');
            $("#asmcta").val('');
            $("#amountmandatory").val('');
            $("#amountright").val('');
            $("#id_titletwo").val('');
            //$("#id_enter").val('');
            $("#note").val('');

        }


        function selectAll(){
            $("input[name='selectbox[]']").prop("checked", true);
        }

        function unSelectAll(){
            $("input[name='selectbox[]']").prop("checked", false);
        }

        function divlineditels(){
            var selectbox = $("input[name='selectbox[]']:checked");
            //alert(selectbox.length)
            let firsdata = $(selectbox [0]).data('titletwo');
            let idline = $(selectbox [0]).val();
            if(firsdata==0){
                Swal.fire('נא לבחור שורות מהטבלה', '', 'info')
                return false;
            }
            for (let i=0;i<selectbox.length;i++){
                if($(selectbox [i]).data('titletwo')!=firsdata){
                    Swal.fire('נא לבחור שורות מאותו סוג תנועה');
                    return false;
                }
            }
            dataObj = {};
            dataObj['idline']= $("#statos").val();

            let url= '{{ route('linebanks.showrowdetils',$bank['id_bank']) }}';
            url +="/"+idline;
            //alert(url);
            let resultAjax = SendToAjax(url,'GET','-1');
            if(resultAjax==undefined){
                notify('حدث خطأ','error');
                return false;
            }

            Swal.fire({
                title: '<strong>חלוקת שורה</strong>',
                //icon: 'info',
                html: resultAjax['html'],
                width: 1000,
                showDenyButton: true,
                //showCancelButton: true,
                confirmButtonText: 'حفظ',
                denyButtonText: `الغاء`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    var dataObj = {};
                    document.querySelectorAll('.inptrowdetl').forEach(function(el){
                        dataObj[el.id]= el.value;
                    });
                    dataObj['scome']='1';
                    //console.log(dataObj);
                    var counterSave=0;
                    for (let i=0;i<selectbox.length;i++) {
                        var idline_aj = $(selectbox[i]).val();
                        let url= '{{ route('linedetail.storemultirowajax',$bank['id_bank']) }}';
                        url +="/"+idline_aj;
                        let resultAjax = SendToAjax(url,'POST',null,dataObj);
                        //console.log(resultAjax);
                        if(resultAjax!=undefined && resultAjax['status']!=undefined && resultAjax['status']==true){
                            counterSave++;
                            $(selectbox[i]).prop("checked", false);
                        }
                    }

                    Swal.fire(" נמשרו " + counterSave + " שורות מתוך " + selectbox.length + " שורות "  )
                } else if (result.isDenied) {
                    Swal.fire('שינוי לא בוצע', '', 'info')
                }
            })


        }
    </script>


