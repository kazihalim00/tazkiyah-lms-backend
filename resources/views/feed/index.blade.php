@extends('layouts.app')

@section('title', 'Community Feed')
@section('header_title', 'Tazkiyah Feed')

@section('content')
    <div class="max-w-2xl mx-auto space-y-8">

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-start gap-4">
                @if(auth()->user()->image)
                    <img src="{{ auth()->user()->image_url }}" class="h-11 w-11 rounded-full object-cover border border-gray-100 shadow-sm shrink-0" alt="My Profile">
                @else
                    <div class="h-11 w-11 bg-indigo-50 text-indigo-700 rounded-full flex items-center justify-center font-black text-sm uppercase shadow-inner shrink-0">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif

                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="w-full space-y-4">
                   @csrf
                    <textarea name="content" rows="3" required
                        class="w-full border-0 focus:ring-0 p-2 text-gray-700 text-base placeholder-gray-400 bg-gray-50 rounded-2xl focus:outline-none"
                        placeholder="Share an Islamic reminder or thought, {{ auth()->user()->name }}..."></textarea>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                        <label class="flex items-center gap-2 text-gray-500 hover:text-indigo-600 font-bold text-sm cursor-pointer transition">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Photo</span>
                            <input type="file" name="image" class="hidden" accept="image/*">
                        </label>

                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm px-6 py-2.5 rounded-xl transition shadow-md">
                            Publish Post
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($posts as $post)
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-4">
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($post->user->image)
                                    <img src="{{ $post->user->image_url }}" class="h-11 w-11 rounded-full object-cover border border-gray-100 shadow-sm" alt="Avatar">
                                @else
                                    <div class="h-11 w-11 bg-indigo-50 text-indigo-700 rounded-full flex items-center justify-center font-black text-sm uppercase shadow-inner">
                                        {{ substr($post->user->name, 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <h4 class="font-extrabold text-gray-900 text-sm tracking-tight">
                                        {{ $post->user->name }}
                                        @if($post->user_id == auth()->id())
                                            <span class="text-[10px] bg-indigo-50 text-indigo-600 font-bold px-2 py-0.5 rounded-full ml-1 uppercase">You</span>
                                        @endif
                                    </h4>
                                    <p class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-wide">
                                        {{ $post->created_at->diffForHumans() }} • <span class="text-indigo-600 font-black">{{ $post->user->level ?? 'Beginner' }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if(auth()->id() == $post->user_id || auth()->user()->role == 'admin')
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this post?')" 
                                                class="text-red-500 hover:text-red-700 text-xs font-bold flex items-center gap-1 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('posts.report', $post->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Do you want to report this post to the admin?')" 
                                                class="text-amber-600 hover:text-amber-800 text-xs font-bold flex items-center gap-1 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            Report
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="text-gray-800 text-base leading-relaxed whitespace-pre-line px-1">
                            {{ $post->content }}
                        </div>

                        @if($post->image)
                            <img src="{{ $post->image_url }}" class="w-full rounded-2xl mt-2" alt="Post media">
                        @endif
                        
                        <div class="flex items-center justify-between text-xs font-bold text-gray-400 px-1 pt-2 border-b border-gray-50 pb-2">
                            <div class="flex items-center gap-1">
                                🤝 <span class="text-gray-600">{{ $post->likes->count() }}</span> Supports
                            </div>
                            <div>
                                <span class="text-gray-600">{{ $post->comments->count() }}</span> Comments
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-1">
                            @php $hasLiked = $post->isLikedBy(auth()->id()); @endphp
                            <form action="{{ route('posts.like', $post->id) }}" method="POST" class="w-1/2">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-extrabold transition flex items-center justify-center gap-2 {{ $hasLiked ? 'bg-indigo-50 text-indigo-600' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                                    <svg class="w-5 h-5" fill="{{ $hasLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.757a2 2 0 011.91 1.39l1.24 3.72A2 2 0 0120.001 18H14v2a2 2 0 01-2 2v-4H9V10h5z"></path>
                                    </svg>
                                    <span>{{ $hasLiked ? 'Supported' : 'Support' }}</span>
                                </button>
                            </form>

                            <div class="w-1/2 py-2.5 bg-gray-50 text-gray-500 rounded-xl text-sm font-extrabold flex items-center justify-center gap-2 cursor-pointer hover:bg-gray-100"
                                 onclick="document.getElementById('main-comment-input-{{ $post->id }}').focus()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Comment</span>
                            </div>
                        </div>

                        <div class="space-y-4 bg-gray-50/70 p-4 rounded-2xl mt-2">

                            @if($post->comments->whereNull('parent_id')->count() > 0)
                                <div class="space-y-4 max-h-80 overflow-y-auto pr-1">
                                    @foreach($post->comments->whereNull('parent_id') as $comment)
                                        <div class="space-y-2">

                                            <div class="flex items-start gap-2.5">
                                                @if($comment->user->image)
                                                    <img src="{{ $comment->user->image_url }}" class="h-7 w-7 rounded-full object-cover shadow-sm mt-0.5" alt="Commenter">
                                                @else
                                                    <div class="h-7 w-7 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-black text-[10px] uppercase mt-0.5">
                                                        {{ substr($comment->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="bg-white p-3 rounded-2xl border border-gray-100 text-sm max-w-[85%] shadow-sm w-full">
                                                    <span class="block font-black text-xs text-gray-900 mb-0.5">{{ $comment->user->name }}</span>
                                                    <p class="text-gray-700 font-medium leading-relaxed">{{ $comment->content }}</p>

                                                    <div class="flex items-center gap-3 mt-2 text-[10px] font-bold text-gray-400">
                                                        <form action="{{ route('comments.like', $comment->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="{{ $comment->isLikedBy(auth()->id()) ? 'text-indigo-600 font-black' : 'hover:text-indigo-600' }}">
                                                                👍 {{ $comment->likes->count() }} Support
                                                            </button>
                                                        </form>
                                                        <span class="cursor-pointer hover:text-indigo-600" onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')">
                                                            Reply
                                                        </span>
                                                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($comment->replies->count() > 0)
                                                <div class="pl-10 space-y-2 border-l-2 border-gray-200 ml-3.5">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="flex items-start gap-2">
                                                            @if($reply->user->image)
                                                                <img src="{{ $reply->user->image_url }}" class="h-6 w-6 rounded-full object-cover shadow-sm mt-0.5" alt="Replier">
                                                            @else
                                                                <div class="h-6 w-6 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center font-black text-[9px] uppercase mt-0.5">
                                                                    {{ substr($reply->user->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                            <div class="bg-purple-50/50 p-2.5 rounded-xl text-xs w-full border border-purple-100/30">
                                                                <span class="block font-extrabold text-gray-900 mb-0.5">{{ $reply->user->name }}</span>
                                                                <p class="text-gray-700 font-medium">{{ $reply->content }}</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.reply', $comment->id) }}" method="POST" class="hidden pl-10 flex gap-2 pt-1">
                                                @csrf
                                                <input type="text" name="content" required placeholder="Write a reply..." 
                                                    class="w-full bg-white px-3 py-1.5 rounded-lg text-xs border border-gray-200 focus:outline-none focus:border-indigo-500 text-gray-700">
                                                <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shrink-0 shadow-sm">
                                                    Reply
                                                </button>
                                            </form>

                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <form action="{{ route('comments.store', $post->id) }}" method="POST" class="flex gap-2 pt-2 border-t border-gray-100">
                                @csrf
                                <input type="text" name="content" id="main-comment-input-{{ $post->id }}" required placeholder="Write a respectful comment..." 
                                    class="w-full bg-white px-4 py-2 rounded-xl text-sm border border-gray-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-700">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-black transition">
                                    Submit
                                </button>
                            </form>
                        </div>

                    </div>
            @empty
                <div class="bg-white p-12 rounded-3xl border border-gray-100 text-center flex flex-col items-center justify-center">
                    <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Feed is Empty</h3>
                    <p class="text-gray-500 font-medium text-sm">Be the first one to motivate the community!</p>
                </div>
            @endforelse
        </div>

    </div>
@endsection