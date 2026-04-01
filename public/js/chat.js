
let receiverId = window.receiverId;
let csrfToken = window.csrfToken;
let selectedImage = null;

function scrollBottom() {
    let chatBox = document.getElementById("chatBox");
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
}

function isAtBottom() {
    let chatBox = document.getElementById("chatBox");
    if (!chatBox) return true;
    // Consider "at bottom" if within 80px of the bottom
    return (chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) < 80;
}

// Load messages with scroll preservation
function loadMessages() {
    let chatBox = document.getElementById("chatBox");
    if (!chatBox) return;

    // Capture state BEFORE fetch
    let scrollTop = chatBox.scrollTop;
    let scrollHeight = chatBox.scrollHeight;
    let clientHeight = chatBox.clientHeight;
    let distanceFromBottom = scrollHeight - scrollTop - clientHeight;
    let wasAtBottom = distanceFromBottom < 80;

    fetch('/chat/fetch/' + receiverId)
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;

            if (wasAtBottom) {
                // User was at or near bottom → keep them there
                chatBox.scrollTop = chatBox.scrollHeight;
            } else {
                // User scrolled up → restore their exact position from the bottom
                let newScrollHeight = chatBox.scrollHeight;
                chatBox.scrollTop = newScrollHeight - distanceFromBottom - clientHeight;
            }
        });
}

// Preview image before send
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

// Image modal
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.add('active');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
}

// Send message
document.getElementById('chatForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let messageInput = document.getElementById('messageInput');
    let message = messageInput.value;

    if (message.trim() === '' && !selectedImage) return;

    let formData = new FormData();
    formData.append('_token', csrfToken);
    if (message.trim() !== '') {
        formData.append('message', message);
    }
    if (selectedImage) {
        formData.append('image', selectedImage);
    }

    fetch('/chat/' + receiverId, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
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

// Refresh online status in header and sidebar
function refreshOnlineStatus() {
    fetch('/chat/status/' + receiverId)
        .then(res => res.json())
        .then(data => {
            // Update header status
            let chatStatus = document.querySelector('.chat-header .chat-status');
            if (chatStatus && data.receiver) {
                chatStatus.innerHTML = data.receiver.html;
            }

            // Update sidebar statuses
            if (data.sidebar) {
                data.sidebar.forEach(function (user) {
                    let sidebarUser = document.querySelector('.chat-user[href="/chat/' + user.id + '"] .chat-user-info small');
                    if (sidebarUser) {
                        sidebarUser.innerHTML = user.html;
                    }
                });
            }
        })
        .catch(err => console.error('Status refresh error:', err));
}

// Auto refresh messages every 3 seconds
setInterval(function () {
    loadMessages();
}, 3000);

// Auto refresh online status every 10 seconds
setInterval(function () {
    refreshOnlineStatus();
}, 10000);

// Initial load
scrollBottom();
refreshOnlineStatus();

