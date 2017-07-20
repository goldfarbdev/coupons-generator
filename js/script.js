/**
 * Created by Greg.Goldfarb on 6/20/17.
 */
/*global jQuery, moment*/

$(function($){
    var couponGenerator = $('form#couponGenerator'),
        textInputs = couponGenerator.find('input[type="text"], textarea'),
        formFields = couponGenerator.find('input, textarea'),
        coupon1 = $('#coupon1'),
        coupon1Fields = coupon1.find('input, textarea'),
        coupon2 = $('#coupon2'),
        coupon2Fields= coupon2.find('input, textarea'),
        couponGeneratorSubmit = couponGenerator.find('button[type="submit"]');


    $( "#startDate1, #startDate2" ).datepicker({"dateFormat":"yymmdd" });

    if($('#oneCoupon').is(':checked')){
        coupon2.hide();

    }

    $('input[name="couponCount"]').change(function(){
        if($('#oneCoupon').is(':checked')){
            coupon2.hide();
        } else{
            coupon2.show();
        }
    });

    $(formFields).on('keyup blur change keypress paste', function(){
        validateForm();
    });

    function validateForm() {
        var couponCount = $('input[name="couponCount"]:checked').val(),
            isThisValid,
            fieldValue,
            couponFields = textInputs;
        couponGeneratorSubmit.attr('disabled');

        if(couponCount == 1) {
            coupon2Fields.val('');
            couponFields = coupon1Fields;
        }

        $('.alert-danger').remove();
        couponFields.each(function(){
            var field = $(this),
                valid = ValidateField(field);

            if(valid === false) {
                if(field.is('input')) {
                    field.after('<div class="alert alert-danger">You have entered an invalid date.</div>');
                } else {
                    field.after('<div class="alert alert-danger">You have entered an invalid coupon field.</div>');
                }
            }

        });

        if($('.alert-danger').length > 0){
            couponGeneratorSubmit.attr('disabled', 'disabled');
        } else {
            couponFields.each(function(){
                var value = $(this).val();
                if(!$.trim(value)){
                    couponGeneratorSubmit.attr('disabled', 'disabled');
                    return;
                }
                couponGeneratorSubmit.removeAttr('disabled');
            });
        }

    }

    function ValidateField(field){
        fieldValue = field.val();

        if(field.is('input')){
            isThisValid = ValidateDate(fieldValue);
        } else if(field.is('textarea')) {
            isThisValid = ValidateCouponField(fieldValue);
        }

        if(isThisValid === false) {
            couponGeneratorSubmit.attr('disabled');
        }

        return isThisValid;
    }
    function ValidateDate(dtValue) {
        // var dtRegex = new RegExp(/\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/);
        // return dtRegex.test(dtValue);
        return moment(dtValue, 'YYYYMMDD', true).isValid();
    }

    function ValidateCouponField(fieldValue) {
        return !(!fieldValue);
    }
});