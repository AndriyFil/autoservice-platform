<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Оберіть майстерню</title>
    <link rel="stylesheet" href="{{ asset('css/public-workshops.css') }}">
</head>
<body>
    <main class="page-shell">
        <section class="hero">
            <p class="eyebrow">AutoService Platform</p>
            <h1>Оберіть майстерню</h1>
            <p class="hero__text">
                Виберіть автосервіс зі списку, щоб швидко залишити заявку на ремонт або діагностику.
            </p>
        </section>

        <section class="workshops-panel" aria-labelledby="workshops-title">
            <div class="workshops-panel__header">
                <div>
                    <p class="eyebrow">Доступні автосервіси</p>
                    <h2 id="workshops-title">Куди відправити заявку?</h2>
                </div>
                <span>{{ $workshops->count() }} активних</span>
            </div>

            @if ($workshops->isEmpty())
                <div class="empty-state">
                    <h3>Поки немає активних майстерень</h3>
                    <p>Коли автосервіси зʼявляться в системі, їх можна буде вибрати тут.</p>
                </div>
            @else
                <div class="workshop-grid">
                    @foreach ($workshops as $workshop)
                        <a class="workshop-card" href="{{ route('public.booking', ['workshop' => $workshop->slug]) }}">
                            <span class="workshop-card__icon" aria-hidden="true">
                                {{ mb_substr($workshop->name, 0, 1) }}
                            </span>
                            <span class="workshop-card__body">
                                <strong>{{ $workshop->name }}</strong>
                                <span class="workshop-card__slug">/{{ $workshop->slug }}</span>

                                @if ($workshop->phone || $workshop->email)
                                    <span class="workshop-card__contacts">
                                        @if ($workshop->phone)
                                            {{ $workshop->phone }}
                                        @endif

                                        @if ($workshop->phone && $workshop->email)
                                            ·
                                        @endif

                                        @if ($workshop->email)
                                            {{ $workshop->email }}
                                        @endif
                                    </span>
                                @endif
                            </span>
                            <span class="workshop-card__action">Записатися</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</body>
</html>
