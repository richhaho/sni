/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$( document ).ready(function() {
    
    $('body').on('keyup','input:not(.noucase), textarea:not(.noucase)' ,function(evt) {
        
        var dontucase = [44,46,32,127,8,37,38,39,40,110,188]
        if (contains.call(dontucase, evt.which)) {
        } else {
            var ccpos = $(this).caret(); 
            $(this).val(function (index, val) {
                return val.toUpperCase();
            });
             $(this).caret(ccpos);
        }
    });
    
});


var contains = function(needle) {
    // Per spec, the way to identify NaN is that it is not equal to itself
    var findNaN = needle !== needle;
    var indexOf;

    if(!findNaN && typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function(needle) {
            var i = -1, index = -1;

            for(i = 0; i < this.length; i++) {
                var item = this[i];

                if((findNaN && item !== item) || item === needle) {
                    index = i;
                    break;
                }
            }

            return index;
        };
    }

    return indexOf.call(this, needle) > -1;
};
  