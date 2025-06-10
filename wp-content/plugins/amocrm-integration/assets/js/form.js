jQuery(document).ready(function($) {
        // Трекер времени на сайте
    let timeSpent = 0;
    let startTime = new Date().getTime();
    
    if (sessionStorage.getItem('amocrm_time_spent')) {
        timeSpent = parseInt(sessionStorage.getItem('amocrm_time_spent'));
        $('#amocrm-time-spent').val(timeSpent);
    } else {
        $(window).on('beforeunload', function() {
            timeSpent = Math.round((new Date().getTime() - startTime) / 1000);
            sessionStorage.setItem('amocrm_time_spent', timeSpent);
        });
    }

    // Валидация формы
    function validateForm() {
        let isValid = true;
        
        // Валидация имени
        const name = $('#amocrm-name').val().trim();
        if (!name) {
            $('#name-error').text('Пожалуйста, введите имя');
            isValid = false;
        } else {
            $('#name-error').text('');
        }
        
        // Валидация email
        const email = $('#amocrm-email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            $('#email-error').text('Пожалуйста, введите email');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            $('#email-error').text('Пожалуйста, введите корректный email');
            isValid = false;
        } else {
            $('#email-error').text('');
        }
        
        // Валидация телефона (русский формат: +7XXXXXXXXXX)
        const phone = $('#amocrm-phone').val().trim();
        const phoneRegex = /^\+7\d{10}$/;
        if (!phone) {
            $('#phone-error').text('Пожалуйста, введите телефон');
            isValid = false;
        } else if (!phoneRegex.test(phone)) {
            $('#phone-error').text('Телефон должен быть в формате +7XXXXXXXXXX');
            isValid = false;
        } else {
            $('#phone-error').text('');
        }
        
        // Валидация цены
        const price = $('#amocrm-price').val();
        if (!price) {
            $('#price-error').text('Пожалуйста, укажите цену');
            isValid = false;
        } else if (parseInt(price) < 0) {
            $('#price-error').text('Цена не может быть отрицательной');
            isValid = false;
        } else {
            $('#price-error').text('');
        }
        
        return isValid;
    }

    // Обработка отправки формы
    $('#amocrm-form').on('submit', function(e) {
        e.preventDefault();
        
        // Рассчитываем время, проведённое на сайте
        const currentTimeSpent = timeSpent || Math.round((new Date().getTime() - startTime) / 1000);
        const spentMoreThan30Sec = currentTimeSpent > 30;
        
        var $form = $(this);
        var $message = $('.amocrm-message');
        
        $message.removeClass('error success').text('');
        
        // Проверяем валидацию
        if (!validateForm()) {
            $message.addClass('error').text('Пожалуйста, заполните все поля корректно');
            return;
        }
        
        // Блокируем кнопку отправки
        $('.amocrm-submit').prop('disabled', true).text('Отправка...');
        
        $.ajax({
            url: amocrm_ajax.url,
            type: 'POST',
            data: {
                action: 'amocrm_submit_form',
                security: amocrm_ajax.nonce,
                name: $('#amocrm-name').val().trim(),
                email: $('#amocrm-email').val().trim(),
                phone: $('#amocrm-phone').val().trim(),
                price: $('#amocrm-price').val(),
                time_spent: currentTimeSpent,
                spent_more_than_30: spentMoreThan30Sec ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    $message.addClass('success').text('Заявка успешно отправлена!');
                    $form[0].reset();
                    sessionStorage.removeItem('amocrm_time_spent');
                } else {
                    $message.addClass('error').text('Ошибка: ' + (response.data || 'неизвестная ошибка'));
                }
            },
            error: function(xhr) {
                $message.addClass('error').text('Ошибка при отправке заявки');
                console.error(xhr.responseText);
            },
            complete: function() {
                $('.amocrm-submit').prop('disabled', false).text('Отправить');
            }
        });
    });

    // Маска для телефона
    $('#amocrm-phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            value = '+7' + value.substring(1, 11);
        }
        $(this).val(value);
    });

    // Трекер времени на сайте
    if (sessionStorage.getItem('amocrm_time_spent')) {
        $('#amocrm-time-spent').val(sessionStorage.getItem('amocrm_time_spent'));
    } else {
        const startTime = new Date().getTime();
        $(window).on('beforeunload', function() {
            const timeSpent = Math.round((new Date().getTime() - startTime) / 1000);
            sessionStorage.setItem('amocrm_time_spent', timeSpent);
        });
    }
});