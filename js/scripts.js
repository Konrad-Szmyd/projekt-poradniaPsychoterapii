'use strict';

// $ = alias do obiektu jQuery
$(function(){
    //sposób 1
    // $('nav a').on('click', function(){
    //     const goToSection = '#' + $(this).attr('class');
    //     $('body, html').animate({
    //         scrollTop: $(goToSection).offset().top - 40
    //     },500)
    // })
    
    //sposób 2

    $("a[href^='#']").on('click', function(e){
        const target = $( $(this).attr('href') );
        // console.log(target);
        // console.log(target.offset());
        if(target.length){
            e.preventDefault();
            $('html,body').animate({
                scrollTop: target.offset().top -40
            },600)
        }

    })

    $('form').on('submit',function(event){
        event.preventDefault();

        const mail = $('#mail').val();
        const subject = $('#subject').val();
        const message = $('#message').val();
        //obsluga captcha zgodna z dokumentacja
        const captchaG = grecaptcha.getResponse();

        $.ajax({
            type: $('form').attr('method'),
            url: $('form').attr('action'),
            dataType: 'json',
            data:{
                mail:mail,
                subject:subject,
                message:message,
                captchaG:captchaG
            },
            success: function(response){
                console.log(response);

                const lab1 = $('label:eq(0)');
                const lab2 = $('label:eq(1)');
                const lab3 = $('label:eq(2)');
                const lab4 = $('label:eq(3)');

                const resPlace = $('#resultPlace');
                resPlace.hide();

                lab1.text('Twój e-mail:');
                lab1.removeClass();
                lab2.text('Temat wiadomości:');
                lab2.removeClass();
                lab3.text('Treść wiadomości:');
                lab3.removeClass();
                lab4.empty();
                lab4.removeClass();

                if('success' in response){
                    
                    resPlace.removeClass();
                    resPlace.addClass('alert alert-success');
                    $('#mail').val('');
                    $('#subject').val('');
                    $('#message').val('');
                    resPlace.text(response.success).fadeIn(500).delay(4000).fadeOut(500);



                }else{

                    if('mail' in response){
                        lab1.hide().addClass('alert alert-danger').fadeIn(500).text(response.mail);
                    }
                    if('subject' in response){
                        lab2.hide().addClass('alert alert-danger').fadeIn(500).text(response.subject);
                    }
                    if('content' in response){
                        lab3.hide().addClass('alert alert-danger').fadeIn(500).text(response.content);
                    }
                    if('captcha' in response){
                        lab4.hide().addClass('alert alert-danger').fadeIn(500).text(response.captcha);
                    }
                 }
            }
        });


    })

});