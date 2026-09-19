@extends('backend.layout.admin_layout')

@section('title', 'Chat Konsultasi')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Chat Konsultasi',
            'subtitle' => 'Percakapan langsung dengan masyarakat pelapor',
            'icon' => 'fa-comments',
        ])

        <div class="chat-shell">
            {{-- DAFTAR PERCAKAPAN --}}
            <div class="card chat-list mb-0">
                <div class="card-header">
                    <h4 class="card-title mb-1">Percakapan</h4>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        {{ $users->count() }} percakapan
                    </p>
                </div>
                <div class="card-body p-2">
                    <div id="conversation-list">
                        @forelse ($users as $u)
                            <a href="{{ route('chat.show', $u->id_akun) }}"
                                class="chat-item {{ isset($activeUser) && $activeUser->id_akun == $u->id_akun ? 'active' : '' }}"
                                data-user-id="{{ $u->id_akun }}">
                                <span class="chat-avatar">{{ strtoupper(substr($u->nama, 0, 1)) }}</span>
                                <span class="chat-item-meta">
                                    <span class="chat-item-top">
                                        <span class="chat-item-name">{{ $u->nama }}</span>
                                        <span class="chat-item-time">
                                            {{ \Carbon\Carbon::parse($u->time)->diffForHumans() }}
                                        </span>
                                    </span>
                                    <span class="chat-item-bottom">
                                        <span class="chat-item-preview">{{ Str::limit($u->last_message, 35) }}</span>
                                        @if ($u->unread > 0)
                                            <span class="chat-unread">{{ $u->unread }}</span>
                                        @endif
                                    </span>
                                </span>
                            </a>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa fa-inbox"></i></div>
                                <p class="mb-0">Belum ada percakapan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- JENDELA PESAN --}}
            <div class="card chat-panel mb-0">
                @if (isset($activeUser))
                    <div class="card-header chat-head">
                        <span class="chat-avatar chat-avatar-sm">
                            {{ strtoupper(substr($activeUser->nama, 0, 1)) }}
                        </span>
                        <div>
                            <h4 class="card-title mb-0">{{ $activeUser->nama }}</h4>
                            <p class="mb-0 text-muted" style="font-size: .78rem;">Percakapan konsultasi</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="chat-window" id="chat-messages">
                            @forelse ($chats as $chat)
                                <div class="chat-bubble {{ $chat->sender_id == $adminId ? 'is-me' : '' }}">
                                    <p>{{ $chat->message }}</p>
                                    <span class="chat-bubble-time">
                                        {{ \Carbon\Carbon::parse($chat->sent_at)->format('H:i') }}
                                    </span>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fa fa-comment-dots"></i></div>
                                    <p class="mb-0">Belum ada pesan. Mulai percakapan di bawah.</p>
                                </div>
                            @endforelse
                        </div>

                        <form id="chat-form" class="chat-composer" method="POST" action="{{ route('chat.send') }}">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $activeUser->id_akun }}">
                            <input type="text" name="message" id="chat-input" class="form-control"
                                placeholder="Ketik pesan..." autocomplete="off" required>
                            <button type="submit" class="btn btn-instansi-primary btn-pill" title="Kirim pesan">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="card-body">
                        <div class="chat-window d-flex align-items-center justify-content-center">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa fa-comments"></i></div>
                                <h5 class="mb-1">Pilih percakapan</h5>
                                <p class="mb-0">Pilih pengguna dari daftar untuk memulai chat.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            var adminId = {{ $adminId ?? 'null' }};
            var activeUserId = {{ isset($activeUser) ? $activeUser->id_akun : 'null' }};
            var ws = null;
            var wsHost = window.location.hostname || 'localhost';
            var wsPort = {{ env('WS_PORT', 3021) }};

            function connectWebSocket() {
                try {
                    ws = new WebSocket('ws://' + wsHost + ':' + wsPort);

                    ws.onopen = function() {
                        console.log('Admin WebSocket connected');
                        if (adminId) {
                            ws.send(JSON.stringify({
                                type: 'register',
                                user_id: String(adminId),
                                user_type: 'admin'
                            }));
                        }
                    };

                    ws.onmessage = function(event) {
                        try {
                            var data = JSON.parse(event.data);

                            if (data.type === 'new_message') {
                                var isCurrentChat = (data.sender_id == activeUserId && data.receiver_id == adminId) ||
                                    (data.sender_id == adminId && data.receiver_id == activeUserId);

                                if (isCurrentChat) {
                                    appendMessage(data);
                                }

                                updateConversationList(data);
                            }
                        } catch (e) {
                            console.error('Error parsing WS message:', e);
                        }
                    };

                    ws.onclose = function() {
                        console.log('WS disconnected, reconnecting in 3s...');
                        setTimeout(connectWebSocket, 3000);
                    };

                    ws.onerror = function(err) {
                        console.error('WS error:', err);
                    };
                } catch (e) {
                    console.error('Failed to init WS:', e);
                }
            }

            connectWebSocket();

            function appendMessage(data) {
                var time = new Date(data.sent_at || Date.now()).toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                var isMe = (data.sender_id == adminId);
                var safe = $('<span>').text(data.message).html();

                var html = '<div class="chat-bubble' + (isMe ? ' is-me' : '') + '">' +
                    '<p>' + safe + '</p>' +
                    '<span class="chat-bubble-time">' + time + '</span>' +
                    '</div>';

                $('#chat-messages').append(html);
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }

            function updateConversationList(data) {
                var senderId = data.sender_id;
                var item = $('.chat-item[data-user-id="' + senderId + '"]');

                if (item.length) {
                    item.find('.chat-item-preview').text(data.message);
                    item.find('.chat-item-time').text('Baru saja');

                    if (senderId != activeUserId) {
                        var badge = item.find('.chat-unread');
                        if (badge.length) {
                            var count = parseInt(badge.text()) || 0;
                            badge.text(count + 1);
                        } else {
                            item.find('.chat-item-bottom').append('<span class="chat-unread">1</span>');
                        }
                    }

                    item.prependTo('#conversation-list');
                } else if (senderId != adminId) {
                    // User baru yang belum ada di daftar percakapan
                    window.location.reload();
                }
            }

            @if (isset($activeUser))
                $('#chat-form').on('submit', function(e) {
                    e.preventDefault();
                    var input = $('#chat-input');
                    var message = input.val().trim();
                    if (!message) return;

                    input.prop('disabled', true);

                    if (ws && ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({
                            type: 'send_message',
                            sender_id: adminId,
                            receiver_id: activeUserId,
                            message: message
                        }));
                        input.val('').prop('disabled', false).focus();
                    } else {
                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: $(this).serialize(),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(response) {
                                if (response.success) {
                                    appendMessage({
                                        sender_id: adminId,
                                        receiver_id: activeUserId,
                                        message: message,
                                        sent_at: new Date().toISOString()
                                    });
                                    input.val('');
                                }
                            },
                            error: function() {
                                alert('Gagal mengirim pesan');
                            },
                            complete: function() {
                                input.prop('disabled', false).focus();
                            }
                        });
                    }
                });
            @endif
        });
    </script>
@endpush
