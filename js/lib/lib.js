//comboboxForScrivener
function setCombobox(name){
    // 設定模糊選擇與下拉選擇並存功能
    $.widget( "ui.combobox", {
        _create: function() {
            var input,
                self = this,
                select = this.element.hide(),
                selected = select.children( ":selected" ),
                value = selected.val() ? selected.text() : "",
                wrapper = this.wrapper = $( "<span>" )
                    .addClass( "ui-combobox" )
                    .insertAfter( select );

                input = $( "<input>" )
                    .appendTo( wrapper )
                    .val( value )
                    .addClass( "ui-state-default ui-combobox-input" )
                    .attr("name",'checkScr')
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: function( request, response ) {
                            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                            response( select.children( "option" ).map(function() {
                                var text = $( this ).text();
                                if ( this.value && ( !request.term || matcher.test(text) ) )
                                        if ($(this).attr('class') == 'ver' || $(this).attr('class') == 'ver2') {

                                            return {
                                                label: text.replace(
                                                    new RegExp(
                                                        "(?![^&;]+;)(?!<[^<>]*)(" +
                                                            $.ui.autocomplete.escapeRegex(request.term) +
                                                            ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                                    ), "<strong>$1</strong>" ),
                                                value: text,
                                                option: this,
                                                ck:$(this).attr('class')
                                            };
                                                                                
                                        }else{
                                            return {
                                                label: text.replace(
                                                        new RegExp(
                                                        "(?![^&;]+;)(?!<[^<>]*)(" +
                                                        $.ui.autocomplete.escapeRegex(request.term) +
                                                        ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                                    ), "<strong>$1</strong>" ),
                                                value: text,
                                                option: this
                                            };
                                        }
                            }) );
                        },
                        select: function( event, ui ) {
                            ui.item.option.selected = true;
                            self._trigger( "selected", event, {
                                item: ui.item.option
                            });
                                
                            if (name == 'scrivener_id') {
                                CatchScrivener() ;
                            }else if(name == 'scrivener_bankaccount'){
                                select.trigger("change");
                                var tmp = ui.item.value;  //下拉的值
                                $('[name=case_bankaccount]').val(tmp);
                                $('[name=scrivener_bankaccount]').val(tmp);
                                tmp = tmp.substring(5, 14);
                                $('[name=certifiedid_view]').val(tmp);
                            }
                            
                        },
                        change: function( event, ui ) {
                            if ( !ui.item ) {
                                var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                valid = false;
                                select.children( "option" ).each(function() {
                                    if ( $( this ).text().match( matcher ) ) {
                                        this.selected = valid = true;
                                        return false;
                                    }
                                });
                                if ( !valid ) {
                                    // remove invalid value, as it didn't match anything
                                    $( this ).val( "" );
                                    select.val( "" );
                                    input.data( "autocomplete" ).term = "";
                                    return false;
                                }
                            }
                                
                            if (name == 'scrivener_id') {
                                CatchScrivener() ;
                            }
                                
                                
                        }
                    })
                    .addClass( "ui-widget ui-widget-content ui-corner-left" );

                input.data( "autocomplete" )._renderItem = function( ul, item ) {


                    if ((item.ck == 'ver' || item.ck == 'ver2') && name == 'scrivener_bankaccount') {
                         return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.label + "</a>" )
                        .appendTo( ul )
                        .addClass(item.ck);
                                                                
                    }else{
                         return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.label + "</a>" )
                        .appendTo( ul );
                    }


                };

                $( "<a>" )
                    .attr( "tabIndex", -1 )
                    .attr( "title", "Show All Items" )
                    .appendTo( wrapper )
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass( "ui-corner-all" )
                    .addClass( "ui-corner-right ui-combobox-toggle" )
                    .click(function() {
                        // close if already visible
                        if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                            input.autocomplete( "close" );
                            return;
                        }

                        // work around a bug (likely same cause as #5265)
                        $( this ).blur();

                        // pass empty string as value to search for, displaying all results
                        input.autocomplete( "search", "" );
                        input.focus();
                    });
        },

        destroy: function() {
            this.wrapper.remove();
            this.element.show();
            $.Widget.prototype.destroy.call( this );
        }
    });

    $( "#"+name).combobox() ;
}
//comboboxFroBranch
function setCombobox2(name,index){
    $.widget( "ui.combobox", {
        _create: function() {
            var input,
                self = this,
                select = this.element.hide(),
                selected = select.children( ":selected" ),
                value = selected.val() ? selected.text() : "",
                wrapper = this.wrapper = $( "<span>" )
                    .addClass( "ui-combobox" )
                    .insertAfter( select );

                input = $( "<input>" )
                    .appendTo( wrapper )
                    .val( value )
                    .addClass( "ui-state-default ui-combobox-input" )
                   
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: function( request, response ) {
                            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                            response( select.children( "option" ).map(function() {
                                var text = $( this ).text();
                                if ( this.value && ( !request.term || matcher.test(text) ) )
                                       
                                            return {
                                                label: text.replace(
                                                        new RegExp(
                                                        "(?![^&;]+;)(?!<[^<>]*)(" +
                                                        $.ui.autocomplete.escapeRegex(request.term) +
                                                        ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                                    ), "<strong>$1</strong>" ),
                                                value: text,
                                                option: this
                                            };
                                       
                            }) );
                        },
                        select: function( event, ui ) {
                            ui.item.option.selected = true;
                            self._trigger( "selected", event, {
                                item: ui.item.option
                            });
                               
                            if (name == 'realestate_brand') { //品牌
                                select.trigger("change");

                                // var brand = ui.item.value;  //下拉的值
                                var brand = $('[name=realestate_brand'+index+']').val();
                               
                                if (brand=='2') {     // 選擇無仲介時的處理
                                     // ClearBranch(index);
                                     // console.log('A');
                                    $('[name=realestate_branchcategory'+index+']').val(3);
                                     
                                    $('[name=realestate_branchcategory'+index+']').attr("selected",true) ;
                                    
                                    $('#add').css({'display':''}) ;

                                }else{

                                    $('[name=realestate_branchcategory'+index+']').siblings('.ui-combobox').find('.ui-autocomplete-input').val('');
                                    $('[name=realestate_branchcategory'+index+']').val(0);

                                }
                                 $('[name=realestate_branchcategory'+index+']').combobox("destroy");
                                setCombobox2("realestate_branchcategory",index); //仲介店
                               
                                CatchBrand(index);
                                BrandFeedBackScrivener(brand,index);

                                ChangeBranch(index);
                                CatchBranch(index);
                              
                          
                            }else if(name=='realestate_branchcategory'){ //類型
                                // console.log('AAA');
                                select.trigger("change");
                                CatchBrand(index);
                                ChangeBranch(index);
                                CatchBranch(index);
                                // setTimeout("ChangeBranch()",500) ;
                                // setTimeout("CatchBranch()",500) ;
                                $('#add').css({'display':''}) ;
                            }else if(name == 'realestate_branch'){
                               select.trigger("change");

                               ChangeBranch(index);
                               CatchBranch(index);
                               $("[name='checkCase3']").val("1");//有更改仲介店需確認該仲介案件是否低於三件
                            }
                            
                        },
                        change: function( event, ui ) {
                            if ( !ui.item ) {
                                var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                valid = false;
                                select.children( "option" ).each(function() {
                                    if ( $( this ).text().match( matcher ) ) {
                                        this.selected = valid = true;
                                        return false;
                                    }
                                });
                                if ( !valid ) {
                                    // remove invalid value, as it didn't match anything
                                    $( this ).val( "" );
                                    select.val( "" );
                                    input.data( "autocomplete" ).term = "";
                                    return false;
                                }

                               
                                // ChangeBranch(index);
                            }
                                
                            if(name == 'realestate_branch'){
                                 setCombobox2("realestate_branch",index); //仲介店 
                                  // console.log('changeRealestate_branch');   
                            }else if(name == 'realestate_branchcategory'){
                                // console.log('changeRealestate_branchcategory');
                                 // setCombobox2("realestate_branchcategory",index); //仲介店
                            }
                        }
                    })
                    .addClass( "ui-widget ui-widget-content ui-corner-left" );

                input.data( "autocomplete" )._renderItem = function( ul, item ) {

                    return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.label + "</a>" )
                        .appendTo( ul );
                };

                $( "<a>" )
                    .attr( "tabIndex", -1 )
                    .attr( "title", "Show All Items" )
                    .appendTo( wrapper )
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass( "ui-corner-all" )
                    .addClass( "ui-corner-right ui-combobox-toggle" )
                    .click(function() {
                        // close if already visible
                        if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                            input.autocomplete( "close" );
                            return;
                        }

                        // work around a bug (likely same cause as #5265)
                        $( this ).blur();

                        // pass empty string as value to search for, displaying all results
                        input.autocomplete( "search", "" );
                        input.focus();
                    });
        },

        destroy: function() {
            this.wrapper.remove();
            this.element.show();
            $.Widget.prototype.destroy.call( this );
        }
    });
    // console.log(name);
    $( "[name='"+name+index+"']").combobox() ;
}

//地政士資料頁籤
function CatchScrivener() {
                
    var scr = $('[name="scrivener_bankaccount"] option:selected').val() ;
                  
    var request = $.ajax({  
            url: "/includes/scrivener/bankcodesearch.php",
            type: "POST",
            data: {
                id:$('[name=scrivener_id]').val(),
                bc:$('[name=case_bank]').val()
            },
            dataType: "json"
        });
        request.done( function( data ) {
            var zipNo = '' ;
            $.each(data, function(key,item) {
                if (key == 0) {
                    $.each(item, function(key2,item2) {
                        if (key2 == 'sOffice') {
                            $('[name=scrivener_office]').val(item2);
                        }
                        if (key2 == 'sMobileNum') {
                            $('[name=scrivener_mobilenum]').val(item2);
                        }
                        if (key2 == 'sTelArea') {
                            $('[name=scrivener_telarea]').val(item2);
                        }    
                        if (key2 == 'sTelMain') {
                            $('[name=scrivener_telmain]').val(item2);
                        }    
                        if (key2 == 'sFaxArea') {
                            $('[name=scrivener_faxarea]').val(item2);
                        }    
                        if (key2 == 'sFaxMain') {
                            $('[name=scrivener_faxmain]').val(item2);
                        } 
                        if (key2 == 'sZip1') {
                            $('[name=scrivener_zip]').val(item2);
                            zipNo = item2 ;
                            $('[name=scrivener_zipF]').val(item2.substr(0,3));
                        }
                        if (key2 == 'sCity') {
                            $('#scrivener_countryR').empty() ;
                            $('#scrivener_countryR').html('<select class="input-text-big" name="scrivener_country" id="scrivener_country" disabled="disabled"><option value="' + item2 + '" selected="selected">' + item2 + '</option></select>') ;
                        }
                        if (key2 == 'sArea') {
                            $('#scrivener_areaR').empty() ;
                            $('#scrivener_areaR').html('<select class="input-text-big" name="scrivener_area" id="scrivener_area" disabled="disabled"><option value="' + zipNo + '" selected="selected">' + item2 + '</option></select>') ;
                        }                                       
                        if (key2 == 'sAddress') {
                            $('[name=scrivener_addr]').val(item2);
                        } 
                        if (key2 == 'sRecall') {
                            $('[name=sRecall]').val(item2);
                        }
                        if (key2 == 'sSpRecall') {
                            $('[name=sSpRecall]').val(item2);
                            $('[name=scrivener_sSpRecall]').val(item2);
                                            
                        }
                        if (key2 == 'sFeedbackMoney') {
                            $('[name=sFeedbackMoney]').val(item2);
                        }
                        if (key2 == 'sales') {
                            $("#showSalseS").text(item2);
                        }
                    });
                }
                if (key == 1) {
                    $('[name=scrivener_bankaccount]').children().remove().end();
                        $.each(item, function(key2,item2) {
                            var sel = '' ;
                            if (key2 == scr) { var sel = ' selected="selected"' ; }
                            if (item2.bVersion == 'A') {
                                if (item2.branch == 6) { //城中
                                    $('[name=scrivener_bankaccount]').append('<option' + sel + ' value="'+key2+'" class="ver2">'+key2+'</option>');
                                }else{
                                    $('[name=scrivener_bankaccount]').append('<option' + sel + ' value="'+key2+'" class="ver">'+key2+'</option>');
                                }
                                            
                            }else{
                                $('[name=scrivener_bankaccount]').append('<option' + sel + ' value="'+key2+'">'+key2+'</option>');
                            }

                                         
                        });
                        setCombobox("scrivener_bankaccount");
                        // $('[name=scrivener_bankaccount]').combobox();
                        var id = $('[name="checkcId"]').val() ;

                        if (id != '') {
                            $("[name='checkAcc']").val($('[name="checkcId2"]').val());
                            $("[name='scrivener_bankaccount']").val($('[name="checkcId2"]').val());
                        }
                }
            });
        });

        $.ajax({
            url: '../includes/escrow/getBrandForScr.php',
            type: 'POST',
            dataType: 'html',
            data: {"scrivner":$("[name='scrivener_id']").val(),"cat":'recall2'},
        })
        .done(function(txt) {
            if (txt != 'error') {
                           
                var obj = JSON.parse(txt);
                var html = '';
                for (var i = 0; i < obj.length; i++) {
                                
                    html += obj[i].BrandName+":"+obj[i].sReacllBrand+"%(仲介)、"+obj[i].sRecall+"%(地政士)；";
                                
                }

                $("#ScrivenerFeedSpTxt").html(html);
                           
            }else{
                $("[name='scrivener_BrandScrRecall']").val(0);
                $("[name='scrivener_BrandRecall']").val(0);
            }
          
        });
                
}
//品牌
function CatchBrand(index) {
     $("[name='realestate_branchnum"+index+"']").val('');//清空仲介店的值

     

    var request = $.ajax({  
        url: "/includes/maintain/brandsearch.php",
        type: "POST",
                    data: {
        id:$('[name=realestate_brand'+index+']').val(), 
            category:$('[name=realestate_branchcategory'+index+']').val()
        },
        dataType: "json"
    });
    request.done( function( data ) {
        $.each(data, function(key,item) {
            if (key == 0) {
                $('#realestate_branch'+index+'R').empty() ;
                var selTxt = '<select class="realty_branch'+index+'" name="realestate_branch'+index+'">' ;
                if ($('[name=realestate_branchcategory'+index+']').val() != 3) {
                    selTxt = selTxt +'<option value="0"></option>';
                }
                           
                $.each(item, function(key2,item2) {
                    var bId =  '';
                    var bName = '';
                    $.each(item2, function(key3,item3) {
                        if (key3 == 'bId') {
                            bId = item3;
                        }
                        if (key3 == 'bStore') {
                            bName = item3;
                        }
                    });
                    if ($('[name=realestate_brand'+index+']').val() == 2 && bId == 505) {
                        selTxt = selTxt + '<option value="' + bId + '" selected=selected>' + bName + '</option>' ;

                       
                    }else{
                        selTxt = selTxt + '<option value="' + bId + '">' + bName + '</option>' ;
                    }
                    

                   
                });
                selTxt = selTxt + '</select>' ;
                $('#realestate_branch'+index+'R').html(selTxt) ;
                // console.log('AAA');
                if ($('[name=realestate_brand'+index+']').val() == 2){
                    $('[name="realestate_branchnum"]').val(505);

                 
                }
                setCombobox2("realestate_branch",index); //仲介店


            }
            if (key == 1) {
                var bWholeName;
                var bSerialnum;
                $.each(item, function(key2,item2) {
                    if (key2 == 'bName') {
                        $('[name=realestate_name'+index+']').val(item2)
                    }
                });
            }
        });
    });
}
 //品牌回饋地政士
function BrandFeedBackScrivener(brand,index){
   
    $.ajax({
        url: '../includes/escrow/getBrandForScr.php',
        type: 'POST',
        dataType: 'html',
        data: {"brand": brand,"scrivner":$("[name='scrivener_id']").val(),"cat":'recall'},
    })
    .done(function(txt) {
        if (txt != false) {
             var obj = JSON.parse(txt);
            
            $("[name='scrivener_BrandScrRecall"+index+"']").val(obj.recall);
            $("[name='scrivener_BrandRecall"+index+"']").val(obj.reacllBrand);
        }else{
            $("[name='scrivener_BrandScrRecall"+index+"']").val(0);
            $("[name='scrivener_BrandRecall"+index+"']").val(0);
        }
                        
                       //

    });
}

//把選擇的值傳到realestata_branchnum(仲介店編號欄位)
function ChangeBranch(index) { 
    var value = $('[name=realestate_branch'+index+'] option:selected').val();
    $('[name=realestate_branchnum'+index+']').val(value);            
}

//
function CatchBranch(index) {
    // console.log($('[name=realestate_brand'+index+']').val());
    ClearBranch(index);
    if ($('[name=realestate_brand'+index+']').val() == 2) {
        $('[name=realestate_branchnum'+index+']').val(505);
    }
    $.ajax({  
        url: "/includes/maintain/branchsearch.php",
        type: "POST",
        cache: false,
        data: {
            id:$('[name=realestate_branchnum'+index+']').val(),
            cId:$('[name=certifiedid_view]').val(),
        },
        dataType: "json",
        async: false
    }).done( function( data ) {
                    // console.log(data);
        var zipNo = '' ;
        $.each(data, function(key,item) {
            if (key == 0) {
                $.each(item, function(key2,item2) {
                    if (key2 == 0) {
                        $.each(item2, function(key3,item3) {
                            if (key3 == 'bTelArea') {
                                $('[name=realestate_telarea'+index+']').val(item3);
                            }
                            if (key3 == 'bTelMain') {
                                $('[name=realestate_telmain'+index+']').val(item3);
                            }
                            if (key3 == 'bFaxArea') {
                                $('[name=realestate_faxarea'+index+']').val(item3);
                            }
                            if (key3 == 'bFaxMain') {
                                $('[name=realestate_faxmain'+index+']').val(item3);
                            }
                            if (key3 == 'bZip') {
                                $('[name=realestate_zip'+index+']').val(item3);
                                zipNo = item3 ;
                                $('[name=realestate_zip'+index+'F]').val(item3.substr(0,3));
                            }
                            if (key3 == 'bCity') {
                                $('#realestate_country'+index+'R').empty() ;
                                if (item3 == null) {
                                    $('#realestate_country'+index+'R').html('<select class="input-text-big" name="realestate_country" id="realestate_country" disabled="disabled"><option value="0" selected="selected">縣市</option></select>') ;
                                }
                                else {
                                    $('#realestate_country'+index+'R').html('<select class="input-text-big" name="realestate_country" id="realestate_country" disabled="disabled"><option value="' + item3 + '" selected="selected">' + item3 + '</option></select>') ;
                                }
                            }
                            if (key3 == 'bArea') {
                                $('#realestate_area'+index+'R').empty() ;
                                if (item3 == null) {
                                    $('#realestate_area'+index+'R').html('<select class="input-text-big" name="realestate_area" id="realestate_area" disabled="disabled"><option value="0" selected="selected">鄉鎮市區</option></select>') ;
                                }
                                else {
                                    $('#realestate_area'+index+'R').html('<select class="input-text-big" name="realestate_area" id="realestate_area" disabled="disabled"><option value="' + zipNo + '" selected="selected">' + item3 + '</option></select>') ;
                                }
                            }
                            if (key3 == 'bAddress') {
                                $('[name=realestate_addr'+index+']').val(item3);
                            }
                            //仲介店1
                            if (key3 == 'bStore') {
                                $('#bt'+index).text();

                                $('#bt'+index).text(item3);
                            }
                            //
                            if (key3 == 'bName') {
                                $('[name=realestate_name'+index+']').val(item3);
                            }
                            if (key3 == 'bServiceOrderHas') {
                                if (item3=='1') {
                                    item3 = '有' ;
                                }
                                else {
                                    item3 = '無' ;
                                }
                                if (index == '') {
                                    $('#promissory1').html(item3);
                                }else{
                                    $('#promissory'+(index+1)+'').html(item3);
                                }
                                
                            }
                            if (key3 == 'bId') {
                                $("[name=realestate_branch"+index+"]").children().each( function(key4, item4){
                                    if (index == '') {
                                        if ($(item4).val() == item3){
                                            $(item4).attr("selected","true"); 

                                            if (index == '') {
                                                  /* 若為非仲介成交店家 */
                                                if (item3 == 505) {     //非仲介成交選擇時，將回饋對象改勾選為地政士
                                                    $('#FBT2').prop('checked',true) ;
                                                    $('#bank505').show();
                                                }
                                                else {
                                                    $('#FBT1').prop('checked',true) ;
                                                    $('#bank505').hide();
                                                }
                                            }
                                          
                                                        ////
                                        }
                                    }else{
                                        if ($(item4).val() == item3){
                                            $(item4).attr("selected","true"); 
                                        }
                                    }
                                    
                                });
                            }
                            if (key3 == 'bRecall') {
                                $('[name=realestate_bRecall'+index+']').val(item3);
                                $("#rea_bRecall"+index).text(item3);
                            }

                            if (key3 == 'bScrRecall') {
                                $('[name=realestate_bScrRecall'+index+']').val(item3);
                                $("#rea_bScrRecall"+index).text(item3);
                            }

                            if (key3 =='bStatus') {
                                $('[name=branch_staus'+index+']').val(item3);
                            }
                                        
                            if (key3 == 'feedBackData') {
                                // alert('1');
                                $("#branchFeedData"+index).html(item3);
                            }
                            if (key3 == 'bSerialnum') {
                                $('[name=realestate_serialnumber'+index+']').val(item3);
                            }
                            if (key3 == 'note') {
                                $('[name=Feedback_CashierOrderMemo'+index+']').val(item3);
                            }
                            if (key3 == 'bCooperationHas') {
                                $('[name="data_feedData'+index+'"]').val(item3);
                            }

                            if (key3 == 'bFeedbackMoney') {
                                $('[name="data_bFeedbackMoney'+index+'"]').val(item3);
                            }

                            if (key3 == 'sales') {
                                $('#showSalseB'+index).text(item3);
                            }
                                     
                        });

                       feedback_money() ;
                    }
                });
            }
            if (key == 1) { 
                $.each(item, function(key2,item2) {
                    if (key2 == 'bCategory') {
                        $('[name=realestate_branchcategory'+index+']').val(item2);
                    }
                });
            }
        });
    });
    //幸福家契約用印店
    if (index  == '') {
        if ($('[name="realestate_branchnum1"]').val() > 0) {
            $('[name="cAffixBranch"]').removeAttr('checked');
        }else{
            if ($('[name="realestate_brand"]').val() == 69) {
             
                $('input:radio[name="cAffixBranch"]').filter('[value="b"]').attr('checked',true) ;
            }                   
        }
    }else{
        $('[name="cAffixBranch"]').removeAttr('checked');
    }
    
                
}

//清除仲介店頁籤裡的仲介資料
function ClearBranch(num){
  
    $("[name='realestate_name"+num+"']").val('');
    $("[name='realestate_serialnumber"+num+"']").val('');
    $("[name='realestate_telarea"+num+"']").val('');
    $("[name='realestate_telmain"+num+"']").val('');
    $('[name=realestate_faxarea'+num+']').val('');
    $('[name=realestate_faxmain'+num+']').val('');
    $('[name=realestate_zip'+num+']').val('');
    $('[name=realestate_zipF'+num+']').val('');
    $('#realestate_countryR'+num).html('<select class="input-text-big" name="realestate_country" id="realestate_country" disabled="disabled"><option value="0" selected="selected">縣市</option></select>') ;
    $('#realestate_areaR'+num).html('<select class="input-text-big" name="realestate_area" id="realestate_area" disabled="disabled"><option value="0" selected="selected">鄉鎮市區</option></select>') ;
    $('[name=realestate_addr'+num+']').val('');
    $('#bt'+num).text('');
    $('[name=realestate_name'+num+']').val('');
    $('#promissory'+(num+1)).html('');
    $('[name=realestate_bRecall'+num+']').val('');
    $("#rea_bRecall"+num).text('');
    $('[name=realestate_bScrRecall'+num+']').val('');
    $("#rea_bScrRecall"+num+"").text('');
    $('[name=branch_staus'+num+']').val('');
    $("#branchFeedData"+num).html('');
    $('[name=realestate_serialnumber'+num+']').val('');
    $('[name=Feedback_CashierOrderMemo'+num+']').val('');
    $('[name="data_feedData'+num+'"]').val('');
    $('#showSalseB'+num).text('');
                
}

//動態新增帳戶
function addBankList(type){
    var no = parseInt($("[name='"+type+"_bank_count']").val());
    if (no < 2) { no = 2;}
    var no2 = no+1;
    $("[name='"+type+"_bank_count']").val(no2);
   
    //先將複製對象的combobox清除不然複製出來的會不正常
    $('#'+type+'_bankkey2').combobox("destroy");
    $('#'+type+'_bankbranch2').combobox("destroy");
    var clonedRow = $('.'+type+'copy2').clone(true);

    clonedRow.find('input').val('');
    clonedRow.find('select').val('');
               
    clonedRow.find('span[name="'+type+'text"]').text('('+no2+')');
    clonedRow.find('#'+type+'_bankkey2').attr('id', type+'_bankkey'+no2);//
    clonedRow.find('#'+type+'_bankkey'+no2).attr('onchange', 'Bankchange("'+type+'",'+no2+')');
    clonedRow.find('#'+type+'_bankbranch2').attr('id', type+'_bankbranch'+no2);//.show()
    clonedRow.find('#'+type+'_bankaccnumber2').attr('id', type+'_bankaccnumber'+no2);
    clonedRow.find('#'+type+'_bankaccname2').attr('id', type+'_bankaccname'+no2);

    clonedRow.find('[name="'+type+'_bankkey2[]"]').attr('name', 'new'+type+'_bankkey2[]');
    clonedRow.find('[name="'+type+'_bankbranch2[]"]').attr('name', 'new'+type+'_bankbranch2[]');
    clonedRow.find('[name="'+type+'_bankaccnumber2[]"]').attr('name', 'new'+type+'_bankaccnumber2[]');
    clonedRow.find('[name="'+type+'_bankaccname2[]"]').attr('name', 'new'+type+'_bankaccname2[]');
    clonedRow.find('[name="'+type+'_bankid2[]"]').attr('name', 'new'+type+'_bankid2[]');
    clonedRow.find('[name="'+type+'_bankMoney2[]"]').attr('name', 'new'+type+'_bankMoney2[]');

    clonedRow.find('#'+type+'_cklist2').attr({
        name: 'new'+type+'_cklist2[]',
        id: 'new'+type+'_cklist2'+no2,
        checked:false
    });

    clonedRow.insertAfter('.'+type+'copy'+no+':last').attr('class', ''+type+'copy'+no2+' del'+type+'copy');

    setComboboxNormal(type+'_bankkey2','id');//再把combobox加回來
    setComboboxNormal(type+'_bankbranch2','id');//再把combobox加回來
    setComboboxNormal(type+'_bankkey'+no2,'id');
    setComboboxNormal(type+'_bankbranch'+no2,'id');
}


function GetBankBranchList2(bank, branch, sc) {
    
    
    // console.log('*'+$(branch).attr("id")); //owner_bankbranch2
    $(branch).prop('disabled',true) ;
                
                var request = $.ajax({  
                    url: "/includes/maintain/bankbranchsearch.php",
                    type: "POST",
                    data: {
                        bankcode: $(bank).val()
                    },
                    dataType: "json"
                });
                request.done(function( data ) {
                    $(branch).combobox("destroy");
                    $(branch).children().remove().end();
                    $(branch).append('<option value="">------</option>')
                    $.each(data, function (key, item) {
                        if (key == sc ) {
                            $(branch).append('<option value="'+key+'" selected>'+item+'</option>');
                        } else {
                            $(branch).append('<option value="'+key+'">'+item+'</option>');
                        }
                        
                    });
                    setComboboxNormal($(branch).attr("id"),'id');//再把combobox加回來
                   
                });
                 
                 // console.log('$'+$(branch).attr("id"));
                $(branch).prop('disabled',false) ;

    // 
}

//金額 加,
function setCurrencymoney(){
    $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
}