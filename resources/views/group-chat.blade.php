<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Group Chat {{ $kelas }} | I FOUND</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">

    <style>
    .msg-sender-name {
        font-size: 11px;
        font-weight: 600;
        color: #667eea;
        margin-bottom: 2px;
    }
    body.dark-theme .msg-sender-name {
        color: #818cf8;
    }
    .group-info-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        background: rgba(102,126,234,0.06);
        font-size: 12px;
        color: #888;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    body.dark-theme .group-info-bar {
        background: rgba(129,140,248,0.06);
        color: #999;
        border-bottom-color: rgba(255,255,255,0.04);
    }
    .member-count {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 1px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }
    </style>
</head>
<body>

@include('components.navbar')

<div class="chat-wrapper">
<div class="chat-layout">

    <!-- SIDEBAR -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <a href="/inbox" class="back-to-inbox">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5"></path>
                    <path d="M12 19l-7-7 7-7"></path>
                </svg>
            </a>
            Group Members
        </div>

        @foreach($members as $member)
            <div class="chat-user">
                <div class="chat-user-avatar">
                    @if($member->photo)
                        <img src="{{ asset('storage/' . $member->photo) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    @endif
                </div>
                <div class="chat-user-info">
                    <strong>{{ $member->name }}</strong>
                    <small>{{ $member->kelas }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <!-- MAIN -->
    <div class="chat-main">

        <div class="chat-header">
            <div class="chat-header-info">
                <a href="/inbox" class="back-btn-mobile">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5"></path>
                        <path d="M12 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="chat-header-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    👥
                </div>
                <div>
                    <strong>Group Kelas {{ $kelas }}</strong>
                    <span class="chat-status">
                        {{ $members->count() + 1 }} anggota
                    </span>
                </div>
            </div>
        </div>

        <div class="group-info-bar">
            👥 <span class="member-count">{{ $members->count() + 1 }}</span> anggota kelas {{ $kelas }}
        </div>

        <div class="chat-box" id="chatBox">
            @include('partials.group-chat-messages', ['messages' => $messages])
        </div>

        <!-- Image Preview -->
        <div id="imagePreview" class="image-preview-bar" style="display:none;">
            <img id="previewImg" src="" alt="Preview">
            <button type="button" onclick="cancelImage()" class="cancel-preview">&times;</button>
        </div>

        <div class="chat-input-area">
            <form id="chatForm">
                @csrf
                <label for="imageInput" class="attach-btn" title="Send Photo">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </label>
                <input type="file" id="imageInput" accept="image/*" style="display:none;" onchange="previewImage(event)">
                <input type="text" name="message" id="messageInput" placeholder="Ketik pesan ke group..." autocomplete="off">
                <button type="submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z"></path>
                    </svg>
                </button>
            </form>
        </div>

    </div>
</div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <img id="modalImage" src="" alt="">
</div>

<script>
window.csrfToken = "{{ csrf_token() }}";
let selectedImage = null;

function scrollBottom() {
    let chatBox = document.getElementById("chatBox");
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
}

function isAtBottom() {
    let chatBox = document.getElementById("chatBox");
    if (!chatBox) return true;
    return (chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) < 80;
}

function loadMessages() {
    let chatBox = document.getElementById("chatBox");
    if (!chatBox) return;

    let scrollTop = chatBox.scrollTop;
    let scrollHeight = chatBox.scrollHeight;
    let clientHeight = chatBox.clientHeight;
    let distanceFromBottom = scrollHeight - scrollTop - clientHeight;
    let wasAtBottom = distanceFromBottom < 80;

    fetch('/group-chat/fetch')
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;
            if (wasAtBottom) {
                chatBox.scrollTop = chatBox.scrollHeight;
            } else {
                let newScrollHeight = chatBox.scrollHeight;
                chatBox.scrollTop = newScrollHeight - distanceFromBottom - clientHeight;
            }
        });
}

function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    selectedImage = file;
    const reader = new FileReader();
    reader.onload = function () {
        document.getElementById('previewImg').src = reader.result;
        document.getElementById('imagePreview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function cancelImage() {
    selectedImage = null;
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.add('active');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
}

document.getElementById('chatForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let messageInput = document.getElementById('messageInput');
    let message = messageInput.value;

    if (message.trim() === '' && !selectedImage) return;

    let formData = new FormData();
    formData.append('_token', window.csrfToken);
    if (message.trim() !== '') {
        formData.append('message', message);
    }
    if (selectedImage) {
        formData.append('image', selectedImage);
    }

    fetch('/group-chat/send', {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        messageInput.value = "";
        cancelImage();
        loadMessages();
    })
    .catch(error => console.error(error));
});

// Auto refresh messages every 3 seconds
setInterval(function () {
    loadMessages();
}, 3000);

// Initial scroll
scrollBottom();
</script>

<script src="{{ asset('js/home.js') }}"></script>
<script src="{{ asset('js/theme.js') }}"></script>

</body>
</html>
