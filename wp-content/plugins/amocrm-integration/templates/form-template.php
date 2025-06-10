<div class="amocrm-form-wrapper">
    <form id="amocrm-form" class="amocrm-form">
        <div class="form-group">
            <label for="amocrm-name">Имя</label>
            <input type="text" id="amocrm-name" name="name" required>
        </div>

        <div class="form-group">
            <label for="amocrm-email">Email</label>
            <input type="email" id="amocrm-email" name="email" required>
        </div>

        <div class="form-group">
            <label for="amocrm-phone">Телефон</label>
            <input type="tel" id="amocrm-phone" name="phone" required>
        </div>

        <div class="form-group">
            <label for="amocrm-price">Цена</label>
            <input type="number" id="amocrm-price" name="price" required>
        </div>

        <input type="hidden" name="time_spent" id="amocrm-time-spent" value="0">
        <input type="hidden" name="action" value="amocrm_submit_form">

        <button type="submit" class="amocrm-submit">Отправить</button>

        <div class="amocrm-message"></div>
    </form>
</div>