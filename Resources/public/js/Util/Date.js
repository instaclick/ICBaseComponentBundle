/*global define */
define(
    'ICBaseComponent/Util/Date',
    [
    ],
    function () {
        'use strict';

        /**
         * Constructor
         */
        var UtilDate = function () {

            /**
             * Converts a string representing a date into an Date object.
             *
             * @param string string Represented in ISO8601 format
             *
             * @return object Converted Date object
             */
            this.fromISO8601 = function (string) {
                var regexp = "(\\d\\d\\d\\d)(-)?(\\d\\d)(-)?(\\d\\d)(T)?(\\d\\d)(:)?(\\d\\d)(:)?(\\d\\d)(\\.\\d+)?(Z|([+-])(\\d\\d)(:)?(\\d\\d))",
                    date   = new Date(),
                    offset,
                    match;

                if (!string.toString().match(new RegExp(regexp))) {
                    date.setTime(Date.parse(string));

                    return date;
                }

                match  = string.match(new RegExp(regexp));
                offset = 0;

                date.setUTCDate(1);
                date.setUTCFullYear(parseInt(match[1], 10));
                date.setUTCMonth(parseInt(match[3], 10) - 1);
                date.setUTCDate(parseInt(match[5], 10));
                date.setUTCHours(parseInt(match[7], 10));
                date.setUTCMinutes(parseInt(match[9], 10));
                date.setUTCSeconds(parseInt(match[11], 10));
                date.setUTCMilliseconds((match[12]) ? parseFloat(match[12]) * 1000 : 0);

                if (match[13] !== 'Z') {
                    offset = (match[15] * 60) + parseInt(match[17], 10);
                    offset *= ((match[14] === '-') ? -1 : 1);

                    date.setTime(date.getTime() - offset * 60 * 1000);
                }

                return date;
            };

            /**
             * Return a string formatted date
             *
             * @param {date} date
             *
             * @return {string}
             */
            this.format = function (date) {
                var year    = date.getFullYear().toString(),
                    month   = ('0' + date.getMonth() + 1).slice(-2).toString(),
                    day     = ('0' + date.getDate()).slice(-2).toString();

                return year + '-' + month + '-' + day;
            };
        };

        return UtilDate;
    }
);
