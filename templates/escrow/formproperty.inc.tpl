<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
           $(function() {
                
                        // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
                $( "#dialog:ui-dialog" ).dialog( "destroy" );
		
                var name = $( "#name" ),
                        email = $( "#email" ),
                        password = $( "#password" ),
                        allFields = $( [] ).add( name ).add( email ).add( password ),
                        tips = $( ".validateTips" );

                function updateTips( t ) {
                        tips
                                .text( t )
                                .addClass( "ui-state-highlight" );
                        setTimeout(function() {
                                tips.removeClass( "ui-state-highlight", 1500 );
                        }, 500 );
                }

                function checkLength( o, n, min, max ) {
                        if ( o.val().length > max || o.val().length < min ) {
                                o.addClass( "ui-state-error" );
                                updateTips( "Length of " + n + " must be between " +
                                        min + " and " + max + "." );
                                return false;
                        } else {
                                return true;
                        }
                }

                function checkRegexp( o, regexp, n ) {
                        if ( !( regexp.test( o.val() ) ) ) {
                                o.addClass( "ui-state-error" );
                                updateTips( n );
                                return false;
                        } else {
                                return true;
                        }
                }
		
                $( "#dialog-form" ).dialog({
                        autoOpen: false,
                        height: 300,
                        width: 350,
                        modal: true,
                        buttons: {
                                "Create an account": function() {
                                        var bValid = true;
                                        allFields.removeClass( "ui-state-error" );

                                        bValid = bValid && checkLength( name, "username", 3, 16 );
                                        bValid = bValid && checkLength( email, "email", 6, 80 );
                                        bValid = bValid && checkLength( password, "password", 5, 16 );

                                        bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
                                        // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
                                        bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
                                        bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

                                        if ( bValid ) {
                                                $( "#users tbody" ).append( "<tr>" +
                                                        "<td>" + name.val() + "</td>" + 
                                                        "<td>" + email.val() + "</td>" + 
                                                        "<td>" + password.val() + "</td>" +
                                                "</tr>" ); 
                                                $( this ).dialog( "close" );
                                        }
                                },
                                Cancel: function() {
                                        $( this ).dialog( "close" );
                                }
                        },
                        close: function() {
                                allFields.val( "" ).removeClass( "ui-state-error" );
                        }
                });

                $( "#create-user" )
                        .button()
                        .click(function() {
                                $( "#dialog-form" ).dialog( "open" );
                        });
                $( "#save-all" )
                        .button()
                        .click(function() {
                                $( "#dialog-form" ).dialog( "open" );
                        });
                        
                $('#save-all').live('click', function () {
                   $('#users tbody tr').each( function(i, v) {
                      $(v).attr('td').each(function(i, v){
                        alert(v);
                    });
                   })
                } );
        });
            
        </script>
        <style type="text/css">
        </style>
    </head>
    <body id="dt_example">
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=100,width=650');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
            <br/>
            <div id="menu-lv2">
                                                        
            </div>
            <br/>
            <div id="dialog-form" title="Create new user">
                <p class="validateTips">All form fields are required.</p>
                <form>
                    <fieldset>
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
                    </fieldset>
                </form>
            </div>
            
            <div id="users-contain" class="ui-widget">
                <table id="users" class="ui-widget ui-widget-content">
                    <thead>
                        <tr class="ui-widget-header ">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>john.doe@example.com</td>
                            <td>johndoe1</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br/>
            <button id="create-user">新增項目</button>
            <button id="save-all">儲存全部</button>
        </div>
        <div id="footer">
            <p>2012 第一建築經理股份有限公司 版權所有</p>
        </div>
    </body>
</html>










