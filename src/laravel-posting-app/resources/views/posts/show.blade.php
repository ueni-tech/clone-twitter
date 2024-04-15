@extends('layouts.app')
@section('title', '投稿詳細')
@section('content')
@if(session('flash_message'))
<p>{{ session('flash_message') }}</p>
@endif

<a href="{{ route('posts.index') }}">&lt; 戻る</a>

<article>
  <h2>{{ $post->title }}</h2>
  <p>{{ $post->content }}</p>

  @if($post->user_id === Auth::id())
  <a href="{{ route('posts.edit', $post) }}">編集</a>

  <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('本当に削除してもよろしいですか？')">
    @csrf
    @method('DELETE')

    <button type="submit">削除</button>
  </form>
  @endif
</article>
@endsection