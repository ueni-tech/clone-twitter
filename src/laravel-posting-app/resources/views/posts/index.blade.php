    @extends('layouts.app')

    @section('title', '投稿一覧')

    @section('content')

    @if(session('flash_message'))
    <p>{{ session('flash_message') }}</p>
    @endif

    @if(session('error_message'))
    <p>{{ session('error_message') }}</p>
    @endif

    <a href="{{ route('posts.create') }}">新規投稿</a>

    @if($posts->isNotEmpty())
    @foreach($posts as $post)
    <article>
      <h2>{{ $post->title }}</h2>
      <p>{{ $post->content }}</p>
      <a href="{{ route('posts.show', $post) }}">詳細</a>
      <a href="{{ route('posts.edit', $post) }}">編集</a>

      <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('本当に削除してもよろしいですか？')">
        @csrf
        @method('DELETE')

        <button type="submit">削除</button>
      </form>
    </article>
    @endforeach
    @else
    <p>投稿はありません。</p>
    @endif

    @endsection