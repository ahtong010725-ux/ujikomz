@php
    $admins = $users->where('role', 'admin');
    $regulars = $users->where('role', '!=', 'admin');
@endphp

@if($admins->count() > 0)
    <div class="inbox-section-label">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Admin Support
    </div>
    @foreach($admins as $user)
    <a href="/chat/{{ $user->id }}" class="inbox-item">
        <div class="inbox-avatar admin-avatar">
            @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="">
            @else
                A
            @endif
        </div>
        <div class="inbox-user-info">
            <strong>
                {{ $user->name }}
                <span class="badge-admin">Admin</span>
            </strong>
            <small class="inbox-preview">
                @if($user->unread_count > 0)
                    <span style="color:#2e7d32;font-weight:600;">{{ $user->unread_count }} pesan baru</span>
                @else
                    {{ $user->last_message_preview ?? 'Belum ada pesan' }}
                @endif
            </small>
        </div>
        <div class="inbox-meta">
            @if($user->last_message_time)
                <small class="inbox-time">{{ \Carbon\Carbon::parse($user->last_message_time)->diffForHumans(null, true, true) }}</small>
            @endif
            @if($user->unread_count > 0)
                <div class="unread-badge">{{ $user->unread_count }}</div>
            @endif
        </div>
    </a>
    @endforeach
@endif

{{-- GROUP CHAT KELAS --}}
@auth
@if(auth()->user()->kelas)
    <div class="inbox-section-label">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Group Kelas
    </div>
    @php
        $kelas = auth()->user()->kelas;
        $lastGroupMsg = \App\Models\GroupMessage::where('kelas', $kelas)->latest()->first();
        $memberCount = \App\Models\User::where('kelas', $kelas)->count();
    @endphp
    <a href="/group-chat" class="inbox-item">
        <div class="inbox-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); font-size: 18px;">
            👥
        </div>
        <div class="inbox-user-info">
            <strong>
                Group Kelas {{ $kelas }}
                <span class="badge-admin" style="background: linear-gradient(135deg, #43a047, #2e7d32);">{{ $memberCount }} anggota</span>
            </strong>
            <small class="inbox-preview">
                @if($lastGroupMsg)
                    {{ $lastGroupMsg->sender->name ?? 'User' }}: {{ \Illuminate\Support\Str::limit($lastGroupMsg->message ?: '📷 Photo', 30) }}
                @else
                    Belum ada pesan di group
                @endif
            </small>
        </div>
        <div class="inbox-meta">
            @if($lastGroupMsg)
                <small class="inbox-time">{{ $lastGroupMsg->created_at->diffForHumans(null, true, true) }}</small>
            @endif
        </div>
    </a>
@endif
@endauth

<div class="inbox-section-label">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
    Messages
</div>
@forelse($regulars as $user)
    <a href="/chat/{{ $user->id }}" class="inbox-item">
        <div class="inbox-avatar">
            @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div class="inbox-user-info">
            <strong>{{ $user->name }}</strong>
            <small class="inbox-preview">
                @if($user->unread_count > 0)
                    <span style="color:#2e7d32;font-weight:600;">{{ $user->unread_count }} pesan baru</span>
                @else
                    {{ $user->last_message_preview ?? 'Belum ada pesan' }}
                @endif
            </small>
        </div>
        <div class="inbox-meta">
            @if($user->last_message_time)
                <small class="inbox-time">{{ \Carbon\Carbon::parse($user->last_message_time)->diffForHumans(null, true, true) }}</small>
            @endif
            @if($user->unread_count > 0)
                <div class="unread-badge">{{ $user->unread_count }}</div>
            @endif
        </div>
    </a>
@empty
    <div class="empty-state">
        Belum ada percakapan 😴
    </div>
@endforelse