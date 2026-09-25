@extends('backend.layout.admin_layout')
@section('admin')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>CHAT KONSULTASI</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Menu</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('chat.index') }}">Chat</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-5">
                <div class="card" style="height: 75vh;">
                    <div class="card-header bg-ijo">
                        <h5 class="card-title mb-0 text-white"><i class="fa fa-comments"></i> Percakapan</h5>
                    </div>
                    <div class="card-body p-0" style="overflow-y: auto;">
                        <div class="list-group list-group-flush" id="conversation-list">
                            @forelse ($users as $u)
                                <a href="{{ route('chat.show', $u->id_akun) }}"
                                   class="list-group-item list-group-item-action d-flex align-items-center py-3 px-3 chat-user-item {{ isset($activeUser) && $activeUser->id_akun == $u->id_akun ? 'active-chat' : '' }}"
                                   data-user-id="{{ $u->id_akun }}">
                                    <div class="rounded-circle bg-ijo text-white d-flex align-items-center justify-content-center mr-3"
                                         style="width: 42px; height: 42px; min-width: 42px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($u->nama, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-truncate" style="font-size: 0.95rem;">{{ $u->nama }}</strong>
                                            @if ($u->unread > 0)
                                                <span class="badge bg-danger text-white rounded-pill ml-2" style="font-size: 0.7rem;">{{ $u->unread }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted text-truncate d-block" style="font-size: 0.8rem;">{{ Str::limit($u->last_message, 35) }}</small>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($u->time)->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center p-4 text-muted">
                                    <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada percakapan
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-7">
                <div class="card" style="height: 75vh;">
                    @if (isset($activeUser))
                        <div class="card-header bg-ijo d-flex align-items-center">
                            <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center mr-3"
                                 style="width: 38px; height: 38px; min-width: 38px; font-weight: bold;">
                                {{ strtoupper(substr($activeUser->nama, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 text-white">{{ $activeUser->nama }}</h5>
                            </div>
                        </div>

                        <div class="card-body" id="chat-messages" style="overflow-y: auto; height: calc(75vh - 140px); background: #f4f6f9;">
                            @foreach ($chats as $chat)
                                @if ($chat->sender_id == $adminId)
                                    <div class="d-flex justify-content-end mb-3">
                                        <div style="max-width: 70%; background: #198754; color: #fff; border-radius: 15px 15px 0 15px; padding: 10px 15px;">
                                            <p class="mb-1" style="font-size: 0.9rem;">{{ $chat->message }}</p>
                                            <small style="font-size: 0.7rem; opacity: 0.8;">{{ \Carbon\Carbon::parse($chat->sent_at)->format('H:i') }}</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-start mb-3">
                                        <div style="max-width: 70%; background: #fff; border-radius: 15px 15px 15px 0; padding: 10px 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                            <p class="mb-1" style="font-size: 0.9rem;">{{ $chat->message }}</p>
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($chat->sent_at)->format('H:i') }}</small>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="card-footer bg-white border-top">
                            <form id="chat-form" method="POST" action="{{ route('chat.send') }}">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $activeUser->id_akun }}">
                                <div class="input-group">
                                    <input type="text" name="message" id="chat-input" class="form-control"
                                           placeholder="Ketik pesan..." autocomplete="off" required
                                           style="border-radius: 20px 0 0 20px; border-right: none;">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-hijau"
                                                style="border-radius: 0 20px 20px 0;">
                                            <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="card-body d-flex align-items-center justify-content-center" style="height: 100%;">
                            <div class="text-center text-muted">
                                <i class="fa fa-comments fa-4x mb-3 d-block" style="opacity: 0.3;"></i>
                                <h5>Pilih percakapan</h5>
                                <p>Pilih pengguna dari daftar untuk memulai chat</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .chat-user-item:hover { background: #e8f5e9 !important; }
        .active-chat { background: #e8f5e9 !important; border-left: 3px solid #198754 !important; }
        .btn-hijau { background-color: #198754; color: #fff; border-color: #198754; }
        .btn-hijau:hover { background-color: #146c43; color: #fff; }
        .bg-ijo { background-color: #198754 !important; }
    </style>
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
            var time = new Date(data.sent_at || Date.now()).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            var html;

            if (data.sender_id == adminId) {
                html = '<div class="d-flex justify-content-end mb-3">' +
                    '<div style="max-width: 70%; background: #198754; color: #fff; border-radius: 15px 15px 0 15px; padding: 10px 15px;">' +
                    '<p class="mb-1" style="font-size: 0.9rem;">' + $('<span>').text(data.message).html() + '</p>' +
                    '<small style="font-size: 0.7rem; opacity: 0.8;">' + time + '</small>' +
                    '</div></div>';
            } else {
                html = '<div class="d-flex justify-content-start mb-3">' +
                    '<div style="max-width: 70%; background: #fff; border-radius: 15px 15px 15px 0; padding: 10px 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">' +
                    '<p class="mb-1" style="font-size: 0.9rem;">' + $('<span>').text(data.message).html() + '</p>' +
                    '<small class="text-muted" style="font-size: 0.7rem;">' + time + '</small>' +
                    '</div></div>';
            }

            $('#chat-messages').append(html);
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        function updateConversationList(data) {
            var senderId = data.sender_id;
            var item = $('.chat-user-item[data-user-id="' + senderId + '"]');

            if (item.length) {
                item.find('small.text-muted.text-truncate').text(data.message);
                item.find('small.text-muted:last-child').text('Baru saja');

                if (senderId != activeUserId) {
                    var badge = item.find('.badge');
                    if (badge.length) {
                        var count = parseInt(badge.text()) || 0;
                        badge.text(count + 1);
                    } else {
                        item.find('.d-flex.justify-content-between').append(
                            '<span class="badge bg-danger text-white rounded-pill ml-2" style="font-size: 0.7rem;">1</span>'
                        );
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
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
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
