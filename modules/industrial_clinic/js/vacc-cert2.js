/**
 * Created by gervie on 8/8/2015.
 */

$(document).ready(function(){
    if($('input[name=first_tetanus]').val() == ''){
        $('#rad_tetanus1').attr('checked', true);
        /*$('#rad_tetanus2').attr('disabled', true);
        $('#rad_tetanus3').attr('disabled', true);

        $('input[name=second_tetanus]').attr('disabled', true);
        $('input[name=third_tetanus]').attr('disabled', true);

        $('#tetanus_trigger2').attr('hidden', true);
        $('#tetanus_trigger3').attr('hidden', true);

        $('select[name=tetanus_deltoid2]').attr('disabled', true);
        $('select[name=tetanus_deltoid3]').attr('disabled', true);*/
    }

    if($('input[name=second_tetanus]').val() == '' && $('input[name=first_tetanus]').val() != ''){
        $('#rad_tetanus2').attr('checked', true);
        /*$('#rad_tetanus1').attr('disabled', true);
        $('#rad_tetanus3').attr('disabled', true);

        $('input[name=third_tetanus]').attr('disabled', true);

        $('#tetanus_trigger3').attr('hidden', true);

        $('select[name=tetanus_deltoid3]').attr('disabled', true);*/
    }

    if($('input[name=third_tetanus]').val() == '' && $('input[name=first_tetanus]').val() != '' && $('input[name=second_tetanus]').val() != ''){
        $('#rad_tetanus3').attr('checked', true);
        /*$('#rad_tetanus1').attr('disabled', true);
        $('#rad_tetanus2').attr('disabled', true);*/
    }

    // Hepatitis
    if($('input[name=first_hepatitis]').val() == ''){
        $('#rad_hepatitis1').attr('checked', true);
        /*$('#rad_hepatitis2').attr('disabled', true);
        $('#rad_hepatitis3').attr('disabled', true);

        $('input[name=second_hepatitis]').attr('disabled', true);
        $('input[name=third_hepatitis]').attr('disabled', true);

        $('#hepatitis_trigger2').attr('hidden', true);
        $('#hepatitis_trigger3').attr('hidden', true);

        $('select[name=hepatitis_deltoid2]').attr('disabled', true);
        $('select[name=hepatitis_deltoid3]').attr('disabled', true);*/
    }

    if($('input[name=second_hepatitis]').val() == '' && $('input[name=first_hepatitis]').val() != ''){
        $('#rad_hepatitis2').attr('checked', true);
        /*$('#rad_hepatitis1').attr('disabled', true);
        $('#rad_hepatitis3').attr('disabled', true);

        $('input[name=third_hepatitis]').attr('disabled', true);

        $('#hepatitis_trigger3').attr('hidden', true);

        $('select[name=hepatitis_deltoid3]').attr('disabled', true);*/
    }

    if($('input[name=third_hepatitis]').val() == '' && $('input[name=first_hepatitis]').val() != '' && $('input[name=second_hepatitis]').val() != ''){
        $('#rad_hepatitis3').attr('checked', true);
        /*$('#rad_hepatitis1').attr('disabled', true);
        $('#rad_hepatitis2').attr('disabled', true);*/
    }

});