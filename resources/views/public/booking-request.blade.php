<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Заявка на ремонт авто</title>
    <link rel="stylesheet" href="{{ asset('css/booking-request.css') }}">
</head>
<body>
    <main class="page-shell">
        <section class="hero">
            <div class="hero__content">
                <p class="eyebrow">AutoService Platform</p>
                <h1>Швидка заявка до майстерні</h1>
                <p class="hero__text">
                    Залиште заявку — майстерня звʼяжеться з вами для уточнення деталей і погодження часу.
                </p>
            </div>
            <div class="hero__panel" aria-label="Майстерня">
                <span>Майстерня</span>
                <strong>{{ str_replace('-', ' ', $workshop) }}</strong>
            </div>
        </section>

        <section class="form-card" aria-labelledby="booking-title">
            <div class="form-card__header">
                <div>
                    <p class="eyebrow">Публічна заявка</p>
                    <h2 id="booking-title">Дані для запису</h2>
                </div>
                <p>Поля з позначкою * обовʼязкові.</p>
            </div>

            <div id="successMessage" class="alert alert--success" hidden>
                <strong>Заявку створено.</strong>
                <span>Майстерня звʼяжеться з вами для підтвердження.</span>
                <span id="requestNumber" class="request-number" hidden></span>
            </div>

            <div id="genericError" class="alert alert--error" hidden>
                Не вдалося створити заявку. Спробуйте ще раз або зателефонуйте до майстерні.
            </div>

            <form id="bookingForm" novalidate>
                <fieldset>
                    <legend>Контактна інформація</legend>
                    <div class="grid">
                        <label>
                            <span>Імʼя *</span>
                            <input name="customer_name" type="text" autocomplete="name" required>
                            <small class="field-error" data-error-for="customer_name"></small>
                        </label>

                        <label>
                            <span>Телефон *</span>
                            <input name="customer_phone" type="tel" autocomplete="tel" placeholder="+380501234567" required>
                            <small class="field-error" data-error-for="customer_phone"></small>
                        </label>

                        <label class="grid__wide">
                            <span>Email</span>
                            <input name="customer_email" type="email" autocomplete="email" placeholder="ivan@example.com">
                            <small class="field-error" data-error-for="customer_email"></small>
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Автомобіль</legend>
                    <div class="grid">
                        <label>
                            <span>Марка *</span>
                            <input name="vehicle_brand" type="text" autocomplete="off" placeholder="BMW" required>
                            <small class="field-error" data-error-for="vehicle_brand"></small>
                        </label>

                        <label>
                            <span>Модель *</span>
                            <input name="vehicle_model" type="text" autocomplete="off" placeholder="X5" required>
                            <small class="field-error" data-error-for="vehicle_model"></small>
                        </label>

                        <label>
                            <span>Рік випуску *</span>
                            <input name="vehicle_year" type="number" inputmode="numeric" min="1900" max="{{ now()->year + 1 }}" placeholder="2018" required>
                            <small class="field-error" data-error-for="vehicle_year"></small>
                        </label>

                        <label>
                            <span>Номер авто</span>
                            <input name="vehicle_plate_number" type="text" autocomplete="off" placeholder="AA1234BB">
                            <small class="field-error" data-error-for="vehicle_plate_number"></small>
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Деталі звернення</legend>
                    <div class="grid">
                        <label>
                            <span>Бажана дата</span>
                            <input name="preferred_date" type="date">
                            <small class="field-error" data-error-for="preferred_date"></small>
                        </label>

                        <label class="grid__wide">
                            <span>Що сталося? *</span>
                            <textarea name="problem_description" rows="5" placeholder="Наприклад: стук у передній підвісці, потрібна діагностика" required></textarea>
                            <small class="field-error" data-error-for="problem_description"></small>
                        </label>
                    </div>
                </fieldset>

                <button id="submitButton" class="submit-button" type="submit">
                    <span class="submit-button__text">Надіслати заявку</span>
                    <span class="submit-button__loading" hidden>Надсилаємо...</span>
                </button>
            </form>
        </section>
    </main>

    <script>
        const form = document.getElementById('bookingForm');
        const submitButton = document.getElementById('submitButton');
        const buttonText = submitButton.querySelector('.submit-button__text');
        const buttonLoading = submitButton.querySelector('.submit-button__loading');
        const successMessage = document.getElementById('successMessage');
        const requestNumber = document.getElementById('requestNumber');
        const genericError = document.getElementById('genericError');
        const endpoint = @json(url("/api/workshops/{$workshop}/booking-requests"));

        const setLoading = (isLoading) => {
            submitButton.disabled = isLoading;
            buttonText.hidden = isLoading;
            buttonLoading.hidden = !isLoading;
        };

        const clearMessages = () => {
            genericError.hidden = true;
            successMessage.hidden = true;
            requestNumber.hidden = true;
            requestNumber.textContent = '';

            document.querySelectorAll('.field-error').forEach((element) => {
                element.textContent = '';
            });

            form.querySelectorAll('[aria-invalid="true"]').forEach((element) => {
                element.removeAttribute('aria-invalid');
            });
        };

        const showValidationErrors = (errors) => {
            Object.entries(errors).forEach(([field, messages]) => {
                const errorElement = document.querySelector(`[data-error-for="${field}"]`);
                const input = form.elements[field];

                if (errorElement) {
                    errorElement.textContent = messages.join(' ');
                }

                if (input) {
                    input.setAttribute('aria-invalid', 'true');
                }
            });
        };

        const formPayload = () => {
            const data = Object.fromEntries(new FormData(form).entries());

            Object.keys(data).forEach((key) => {
                if (typeof data[key] === 'string') {
                    data[key] = data[key].trim();
                }

                if (data[key] === '') {
                    delete data[key];
                }
            });

            if (data.vehicle_year) {
                data.vehicle_year = Number(data.vehicle_year);
            }

            return data;
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearMessages();
            setLoading(true);

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formPayload()),
                });

                const body = await response.json().catch(() => ({}));

                if (response.status === 201) {
                    form.reset();
                    successMessage.hidden = false;

                    if (body.data && body.data.id) {
                        requestNumber.textContent = `Номер заявки: ${body.data.id}`;
                        requestNumber.hidden = false;
                    }

                    successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                if (response.status === 422 && body.errors) {
                    showValidationErrors(body.errors);
                    return;
                }

                genericError.hidden = false;
            } catch (error) {
                genericError.hidden = false;
            } finally {
                setLoading(false);
            }
        });
    </script>
</body>
</html>
