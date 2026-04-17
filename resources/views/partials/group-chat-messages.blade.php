@foreach($messages as $msg)
    @if($msg->sender_id == auth()->id())
        <div class="chat-message sent">
            @if($msg->image)
                <div class="msg-image">
                    <img src="{{ asset('storage/' . $msg->image) }}" alt="Photo" onclick="openImageModal(this.src)">
                </div>
            @endif
            @if($msg->message)
                <div class="msg-text">{{ $msg->message }}</div>
            @endif
            <span class="time">
                {{ $msg->created_at->format('H:i') }}
            </span>
        </div>
    @else
        <div class="chat-message received">
            <div class="msg-sender-name">{{ $msg->sender->name ?? 'User' }}</div>
            @if($msg->image)
                <div class="msg-image">
                    <img src="{{ asset('storage/' . $msg->image) }}" alt="Photo" onclick="openImageModal(this.src)">
                </div>
            @endif
            @if($msg->message)
                <div class="msg-text">{{ $msg->message }}</div>
            @endif
            <span class="time">
                {{ $msg->created_at->format('H:i') }}
            </span>
        </div>
    @endif
@endforeach
