@extends('learning.layout', ['title' => $note['title']])

@section('content')
    <section class="hero">
        <span class="badge">Laravel HTTP</span>
        <h1>{{ $note['title'] }}</h1>
        <p>{{ $note['summary'] }}</p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Controller Boundary</h2>
                <p>
                    This page is intentionally small: the route calls one controller, the controller asks one service
                    for data, and the Blade template renders that data.
                </p>
            </article>
            <article class="panel">
                <h2>Next Practice Step</h2>
                <p>{{ $note['next_step'] }}</p>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Review Checklist</h2>
        <div class="list">
            @foreach ($note['checklist'] as $item)
                <article class="item">
                    <p>{{ $item }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection
