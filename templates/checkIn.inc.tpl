
        <script>
        $(function() {
            $.post('/includes/staff/checkInAlert.php', function(response) {
                $("#dialog-message").empty().html(response);
                if (response == '') return;

                $( "#dialog-message" ).dialog({
                    modal: true,
                    title: '簽到提醒',
                    width:700,
                    height:500,
                    buttons: {
                        "關閉": function() {
                            $("#dialog-message").empty()
                            $( this ).dialog( "close" );
                        }
                    }
                });
            });

            <{if !$smarty.session.member_id|in_array: [2, 3] }>
            let el = `<div style="text-align:right;padding: 10px;">
                <button type="button" id="checkIn" class="button" style="padding: 5px;margin-right:100px;" onclick="checkInOut('IN')"><span style="font-size:10pt;">上班簽到</span></button>
                <button type="button" id="checkOut" class="button" style="padding: 5px;" onclick="checkInOut('OUT')"><span style="font-size:10pt;">下班簽退</span></button>
            </div>`;
            $('#header').prepend(el);
            <{/if}>
        });

        function checkInOut(type) {
            storeCheckInOutData(type);

            let request = $.ajax({
                url: "/includes/staff/checkInOut.php",
                type: "POST",
                data: {
                    type: type,
                    from: 1
                },
                dataType: "html"
            });
            request.done(function( msg ) {
                $("#dialog-message").empty().html(msg);

                let title = type == 'IN' ? '簽到' : '簽退';
                $( "#dialog-message" ).dialog({
                    modal: true,
                    title: title,
                    width:300,
                    height:200,
                    buttons: {
                        "關閉": function() {
                            $("#dialog-message").empty()
                            $( this ).dialog( "close" );
                        }
                    }
                });
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( "打卡異常: " + jqXHR.responseText );
            });
        }

        function storeCheckInOutData(type) {
            const keyDate = new Date();

            saveCheckInOutData(keyDate, type);
            deleteExpiredData(keyDate);
        }

        function saveCheckInOutData(keyDate, type) {
            const localstoregeKey = 'checkInOut_' + keyDate.toISOString().split('T')[0];

            let checkInOutData = localStorage.getItem(localstoregeKey);
            if (checkInOutData) {
                checkInOutData = JSON.parse(checkInOutData);
            } else {
                checkInOutData = [];
            }

            let data = {
                type: type,
                date: keyDate.toISOString().split('T')[0],
                time: keyDate.toTimeString().split(' ')[0]
            };

            checkInOutData.push(data);
            localStorage.setItem(localstoregeKey, JSON.stringify(checkInOutData));
        }

        function deleteExpiredData(keyDate) {
            const deleteLocalstorageKey = 'checkInOut_' + new Date(keyDate.setDate(keyDate.getDate() - 10)).toISOString().split('T')[0];

            for (let i = 0; i < localStorage.length; i++) {
                let key = localStorage.key(i);
                
                let pattern = /^checkInOut_/;
                if (pattern.test(key)) {
                    console.log('key: ' + key);
                    
                    if (key < deleteLocalstorageKey) {
                        console.log('deleting ' + key + ' ...');
                        localStorage.removeItem(key);
                    }
                }
            }
        }
        </script>