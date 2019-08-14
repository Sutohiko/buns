$('#phone').inputmask("+7(999)999-99-99");

$('body').on('click', '.new-ocform__submit', function(e) {
    e.preventDefault();
    let proceed = true;
    //Простая валидация на стороне клиента
    $(".new-ocform__form input[required='required'], .new-ocform__form textarea[required='required']").each(function(){
        $(this).css('border-color','');
        if(!$.trim($(this).val())){
            $(this).css('border-color','red');
            proceed = false;
        }
    });

    if (proceed) {
        let output = '';
        $.ajax({
            url: 'index.php?route=ajax/ocform/ajaxSend﻿',
            type: 'post',
            data: $('.new-ocform__form').serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.type === 'error'){
                    output = '<div class="error">'+response.text+'</div>';
                } else {
                    output = '<div class="success">'+response.text+'</div>';
                    $(".new-ocform__form input[required=required], .new-ocform__form textarea").val('');
                }
                $(".new-ocform__form").html(output);
            }
        });
    }
});

//убираем красную рамку у полей, которые начал исправлять пользователь
$(".new-ocform__form input[required='required'], .new-ocform__form textarea[required='required']").keyup(function() {
    $(this).css('border-color','');
});