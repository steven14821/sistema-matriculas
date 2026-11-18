<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="description" content="Consulta la oferta académica de Saint Victoria Academy y accede al portal de matrículas según tu perfil.">
    <title>Saint Victoria Academy | Portal académico</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js'])
</head>
<body class="site-home antialiased">
    <main class="site-home__frame">
        <div class="site-home__paper">
            <div class="site-home__scallop" aria-hidden="true"></div>
            <div class="site-home__inner">
                <header class="site-home__masthead">
                    <a href="{{ route('home') }}" class="site-home__brand" aria-label="Saint Victoria Academy, inicio">
                        <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="" class="site-home__seal">
                        <span class="site-home__brand-copy">
                            <span class="site-home__brand-name">Saint Victoria Academy</span>
                            <span class="site-home__brand-note">Portal académico</span>
                        </span>
                    </a>

                    <nav class="site-home__nav" aria-label="Navegación principal">
                        <a href="#oferta-vigente">Oferta vigente</a>
                        <a href="#perfiles">Perfiles</a>
                    </nav>

                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="site-home__access">
                        {{ auth()->check() ? 'Ir al panel' : 'Iniciar sesión' }}
                    </a>
                </header>

                <section class="site-home__bill" aria-labelledby="home-title">
                    <h1 id="home-title">Conoce la oferta.<br><span>Elige tu grupo.</span></h1>
                    <p class="site-home__intro">
                        Revisa programas, asignaturas y grupos de periodos abiertos. Después, ingresa al portal con tu perfil institucional para continuar tu matrícula.
                    </p>

                    <dl class="site-home__facts" aria-label="Resumen de la oferta académica">
                        <div>
                            <dd>{{ $programas->count() }}</dd>
                            <dt>Programas</dt>
                        </div>
                        <div>
                            <dd>{{ $asignaturas->count() }}</dd>
                            <dt>Asignaturas</dt>
                        </div>
                        <div>
                            <dd>{{ $grupos->count() }}</dd>
                            <dt>Grupos en periodos abiertos</dt>
                        </div>
                    </dl>

                    <div class="site-home__actions">
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="site-home__primary">
                            <span>{{ auth()->check() ? 'Continuar al panel' : 'Entrar al portal' }}</span>
                            <svg viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M3.5 10h12m-5-5 5 5-5 5"/>
                            </svg>
                        </a>
                        <a href="#oferta-vigente" class="site-home__secondary">Ver grupos del periodo</a>
                    </div>
                    <p class="site-home__access-note">El acceso y las opciones disponibles dependen de tu perfil institucional.</p>
                </section>

                <section id="oferta-vigente" class="site-home__offer" aria-labelledby="offer-title">
                    <div class="site-home__offer-heading">
                        <h2 id="offer-title">Una mirada a la oferta.</h2>
                        <p>Asignaturas y grupos asociados a periodos que se encuentran abiertos.</p>
                    </div>

                    <div class="site-home__offer-content">
                        <div class="site-home__group-list">
                            @forelse ($grupos->take(5) as $grupo)
                                <article class="site-home__group">
                                    <span class="site-home__group-code">Grupo {{ $grupo->codigo }}</span>
                                    <h3>{{ $grupo->asignatura?->nombre }}</h3>
                                    <span class="site-home__group-period">{{ $grupo->periodo?->nombre }}</span>
                                </article>
                            @empty
                                <p class="site-home__empty">
                                    No hay grupos asociados a periodos abiertos en este momento. Vuelve a consultar más adelante.
                                </p>
                            @endforelse
                        </div>

                        <aside class="site-home__programs" aria-labelledby="programs-title">
                            <h3 id="programs-title">Programas del catálogo</h3>
                            @forelse ($programas->take(4) as $programa)
                                <div class="site-home__program">
                                    <span>{{ $programa->nombre }}</span>
                                    <span>{{ $programa->asignaturas_count }} {{ $programa->asignaturas_count === 1 ? 'asignatura' : 'asignaturas' }}</span>
                                </div>
                            @empty
                                <p class="site-home__empty">El catálogo de programas aún no tiene registros.</p>
                            @endforelse
                            @if ($programas->count() > 4)
                                <p class="site-home__more-programs">Y {{ $programas->count() - 4 }} programas más en el catálogo.</p>
                            @endif
                        </aside>
                    </div>
                    <a href="{{ auth()->check() ? route('oferta.index') : route('login') }}" class="site-home__offer-link">
                        {{ auth()->check() ? 'Consultar toda la oferta' : 'Inicia sesión para consultar la oferta completa' }}
                    </a>
                </section>

                <section id="perfiles" class="site-home__profiles" aria-labelledby="profiles-title">
                    <h2 id="profiles-title">Un portal, distintos recorridos.</h2>
                    <p class="site-home__profiles-intro">Cada perfil encuentra las herramientas que necesita para su trabajo académico.</p>

                    <div class="site-home__role-list">
                        <article class="site-home__role">
                            <h3>Estudiantes</h3>
                            <p>Consulta grupos disponibles, gestiona tu matrícula y revisa tu recorrido académico.</p>
                        </article>
                        <article class="site-home__role">
                            <h3>Funcionarios</h3>
                            <p>Administra la oferta académica y acompaña los procesos de matrícula.</p>
                        </article>
                        <article class="site-home__role">
                            <h3>Administradores</h3>
                            <p>Gestiona usuarios y consulta el catálogo y la información académica.</p>
                        </article>
                    </div>
                </section>

                <section class="site-home__closing" aria-labelledby="closing-title">
                    <h2 id="closing-title">El siguiente paso está en el portal.</h2>
                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="site-home__closing-link">
                        {{ auth()->check() ? 'Continuar al panel' : 'Ingresar con mi perfil' }}
                        <svg viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M3.5 10h12m-5-5 5 5-5 5"/>
                        </svg>
                    </a>
                </section>

                <footer class="site-home__footer">
                    <span>© {{ date('Y') }} Saint Victoria Academy</span>
                    <span>Oferta académica · Matrículas · Seguimiento</span>
                </footer>
            </div>
        </div>
    </main>
</body>
</html>
