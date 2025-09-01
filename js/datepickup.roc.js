$.extend($.datepicker,{

    /* Parse existing date and initialise date picker. */

    _setDateFromField: function(inst, noDefault) {

        if (inst.input.val() == inst.lastVal) {

            return;

        }

        var dateFormat = this._get(inst, 'dateFormat');

        var dates = inst.lastVal = inst.input ? inst.input.val() : null;

        var date, defaultDate;

        date = defaultDate = this._getDefaultDate(inst);

        var settings = this._getFormatConfig(inst);

        try {

            //date = this.parseDate(dateFormat, dates, settings) || defaultDate;

            if(dates.couny>0)

            {

                var dateArr = dates.split("/");

                var year = parseInt(dateArr[0], 10) + 1911;

                var month = parseInt(dateArr[1], 10);

                var day = parseInt(dateArr[2], 10);

                date = new Date(year, month, day);

            }

        } catch (event) {

            this.log(event);

            dates = (noDefault ? '' : dates);

        }

        inst.selectedDay = date.getDate();

        inst.drawMonth = inst.selectedMonth = date.getMonth();

        inst.drawYear = date.getFullYear();

        inst.selectedYear = date.getFullYear();

        inst.currentDay = (dates ? date.getDate() : 0);

        inst.currentMonth = (dates ? date.getMonth() : 0);

        inst.currentYear = (dates ? date.getFullYear() : 0);

        this._adjustInstDate(inst);

    },

    _daylightSavingAdjust: function(date) {

        if (!date) return null;

        date.setHours(date.getHours() > 12 ? date.getHours() + 2 : 0);

 

        if (!date) return null;

        if((date.getFullYear()-1911)>0)

            date.getFullYear((date.getFullYear()-1911));

        else

            date.getFullYear((date.getFullYear()));

 

        return date;

    },

    _taiwanDateAdjust: function(date) {

        if (!date) return null;

        if((date.getFullYear()-1911)>0)

            date.setFullYear((date.getFullYear()-1911),date.getMonth(),date.getDay());

        else

            date.setFullYear((date.getFullYear()),date.getMonth(),date.getDay());

        return date;

    },

    /* Generate the month and year header. */

    _generateMonthYearHeader: function(inst, drawMonth, drawYear, minDate, maxDate,

        secondary, monthNames, monthNamesShort) {

        var changeMonth = this._get(inst, 'changeMonth');

        var changeYear = this._get(inst, 'changeYear');

        var showMonthAfterYear = this._get(inst, 'showMonthAfterYear');

        var html = '<div class="ui-datepicker-title">';

        var monthHtml = '';

        // month selection

        if (secondary || !changeMonth)

            monthHtml += '<span class="ui-datepicker-month">' + monthNames[drawMonth] + '</span>';

        else {

            var inMinYear = (minDate && minDate.getFullYear() == drawYear);

            var inMaxYear = (maxDate && maxDate.getFullYear() == drawYear);

            monthHtml += '<select class="ui-datepicker-month" ' +

            'onchange="DP_jQuery_' + dpuuid + '.datepicker._selectMonthYear(\'#' + inst.id + '\', this, \'M\');" ' +

            '>';

            for (var month = 0; month < 12; month++) {

                if ((!inMinYear || month >= minDate.getMonth()) &&

                    (!inMaxYear || month <= maxDate.getMonth()))

                    monthHtml += '<option value="' + month + '"' +

                    (month == drawMonth ? ' selected="selected"' : '') +

                    '>' + monthNamesShort[month] + '</option>';

            }

            monthHtml += '</select>';

        }

        if (!showMonthAfterYear)

            html += monthHtml + (secondary || !(changeMonth && changeYear) ? '&#xa0;' : '');

        // year selection

        if ( !inst.yearshtml ) {

            inst.yearshtml = '';

            if (secondary || !changeYear)

                if((drawYear-1911) >0)

                    html += '<span class="ui-datepicker-year">' + (drawYear-1911) + '</span>';

                else

                    html += '<span class="ui-datepicker-year">' + drawYear + '</span>';

            else {

                // determine range of years to display

                var years = this._get(inst, 'yearRange').split(':');

                var thisYear = new Date().getFullYear();

                var determineYear = function(value) {

                    var year = (value.match(/c[+-].*/) ? drawYear + parseInt(value.substring(1), 10) :

                        (value.match(/[+-].*/) ? thisYear + parseInt(value, 10) :

                            parseInt(value, 10)));

                    return (isNaN(year) ? thisYear : year);

                };

                var year = determineYear(years[0]);

                var endYear = Math.max(year, determineYear(years[1] || ''));

                year = (minDate ? Math.max(year, minDate.getFullYear()) : year);

                endYear = (maxDate ? Math.min(endYear, maxDate.getFullYear()) : endYear);

                inst.yearshtml += '<select class="ui-datepicker-year" ' +

                'onchange="DP_jQuery_' + dpuuid + '.datepicker._selectMonthYear(\'#' + inst.id + '\', this, \'Y\');" ' +

                '>';

 

                if((drawYear-1911) >0)

                {

                    for (; year <= endYear; year++) {

                        inst.yearshtml += '<option value="' + year + '"' +

                        (year == drawYear ? ' selected="selected"' : '') +

                        '>' + (year-1911) + '</option>';

                    }

                }

                else

                {

                    for (; year <= endYear; year++) {

                        inst.yearshtml += '<option value="' + year + '"' +

                        (year == drawYear ? ' selected="selected"' : '') +

                        '>' + (year) + '</option>';

                    }

                }

                inst.yearshtml += '</select>';

 

                html += inst.yearshtml;

                inst.yearshtml = null;

            }

        }

        html += this._get(inst, 'yearSuffix');

        if (showMonthAfterYear)

            html += (secondary || !(changeMonth && changeYear) ? '&#xa0;' : '') + monthHtml;

        html += '</div>'; // Close datepicker_header

        return html;

    },

    _formatDate : function(inst, day, month, year)

 

    {

 

        if (!day)

 

        {

 

            inst.currentDay = inst.selectedDay;

 

            inst.currentMonth = inst.selectedMonth;

 

            inst.currentYear = inst.selectedYear;

 

        }

 

        var date = (day ? (typeof day == 'object' ? day :

 

            this._daylightSavingAdjust(new Date(year, month, day))) :

 

        this._daylightSavingAdjust(new Date(inst.currentYear, inst.currentMonth, inst.currentDay)));

 

        return (date.getFullYear() - 1911) + "/" +

 

        (date.getMonth() < 9 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1)) + "/" +

 

        (date.getDate() < 10 ? "0" + date.getDate() : date.getDate());

 

    }

});