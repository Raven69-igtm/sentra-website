@extends('backend.layout.admin_layout')

@section('title', 'Chat Konsultasi')

@push('styles')
    <style>
        .chat-reply-quote {
            border-left: 3px solid rgba(0, 0, 0, .18);
            background: rgba(0, 0, 0, .05);
            border-radius: 6px;
            padding: 4px 8px;
            margin-bottom: 6px;
            font-size: .8rem;
            color: #555;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .chat-reply-btn {
            border: 0;
            background: transparent;
            color: #6b7280;
            font-size: .72rem;
            padding: 2px 0 0;
            cursor: pointer;
            display: block;
        }

        .chat-reply-btn:hover {
            color: #111827;
            text-decoration: underline;
        }

        .chat-reply-preview {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 10px;
            margin: 0 0 6px;
            border-left: 3px solid #198754;
            background: #f1f8f4;
            border-radius: 6px;
            font-size: .8rem;
            color: #374151;
        }

        .chat-reply-preview-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-reply-cancel {
            border: 0;
            background: transparent;
            font-size: 1.1rem;
            line-height: 1;
            color: #6b7280;
            cursor: pointer;
        }
    </style>
@endpush

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Chat Konsultasi',
            'subtitle' => 'Percakapan langsung dengan masyarakat pelapor',
            'icon' => 'fa-comments',
        ])

        {{-- Klik percakapan dirender instan dari cache di browser. Area live
             hanya dipakai untuk menyegarkan daftar saat ada pengirim baru. --}}
        <div class="chat-shell">
            {{-- DAFTAR PERCAKAPAN --}}
            <div class="card chat-list mb-0" data-live-region="ppa-chat-list">
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
            {{-- Panel dibuat sebagai kerangka tetap: perpindahan percakapan
                 hanya mengubah isinya dari cache di browser (instan). --}}
            <div class="card chat-panel mb-0" id="chat-panel">
                <div class="card-header chat-head @if (!isset($activeUser)) d-none @endif" id="chat-head">
                    <span class="chat-avatar chat-avatar-sm" id="chat-head-avatar">
                        {{ isset($activeUser) ? strtoupper(substr($activeUser->nama, 0, 1)) : '' }}
                    </span>
                    <div>
                        <h4 class="card-title mb-0" id="chat-head-name">{{ $activeUser->nama ?? '' }}</h4>
                        <p class="mb-0 text-muted" style="font-size: .78rem;">Percakapan konsultasi</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chat-window @if (!isset($activeUser)) d-none @endif" id="chat-messages">
                        @if (isset($activeUser))
                            @forelse ($chats as $chat)
                                <div class="chat-bubble {{ $chat->sender_id == $adminId ? 'is-me' : '' }}"
                                    data-chat-id="{{ $chat->id_chat }}">
                                    @if ($chat->repliedTo)
                                        <div class="chat-reply-quote">
                                            {{ \Illuminate\Support\Str::limit($chat->repliedTo->message, 120) }}
                                        </div>
                                    @endif
                                    <p>{{ $chat->message }}</p>
                                    <span class="chat-bubble-time">
                                        {{ \Carbon\Carbon::parse($chat->sent_at)->format('H:i') }}
                                    </span>
                                    <button type="button" class="chat-reply-btn"
                                        data-reply-id="{{ $chat->id_chat }}"
                                        data-reply-text="{{ $chat->message }}">
                                        Balas
                                    </button>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fa fa-comment-dots"></i></div>
                                    <p class="mb-0">Belum ada pesan. Mulai percakapan di bawah.</p>
                                </div>
                            @endforelse
                        @endif
                    </div>

                    <div class="chat-window d-flex align-items-center justify-content-center @if (isset($activeUser)) d-none @endif"
                        id="chat-placeholder">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fa fa-comments"></i></div>
                            <h5 class="mb-1">Pilih percakapan</h5>
                            <p class="mb-0">Pilih pengguna dari daftar untuk memulai chat.</p>
                        </div>
                    </div>

                    <div id="chat-reply-preview" class="chat-reply-preview d-none">
                        <span class="chat-reply-preview-text"></span>
                        <button type="button" id="chat-reply-cancel" class="chat-reply-cancel"
                            aria-label="Batalkan balasan">&times;</button>
                    </div>

                    <form id="chat-form" class="chat-composer @if (!isset($activeUser)) d-none @endif"
                        method="POST" action="{{ route('chat.send') }}">
                        @csrf
                        <input type="hidden" name="receiver_id" id="chat-receiver"
                            value="{{ isset($activeUser) ? $activeUser->id_akun : '' }}">
                        <input type="hidden" name="replied_to_id" id="chat-replied-to" value="">
                        <input type="text" name="message" id="chat-input" class="form-control"
                            placeholder="Ketik pesan..." autocomplete="off" required>
                        <button type="submit" class="btn btn-instansi-primary btn-pill" title="Kirim pesan">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="application/json" id="chat-prefetch">@json($chatPrefetch ?? [])</script>
    <script>
        // Skrip halaman ikut dieksekusi ulang setiap soft navigation. Koneksi
        // WebSocket halaman sebelumnya ditutup lebih dulu lewat event
        // livewire:navigating agar tidak ada socket yang terus reconnect di
        // latar belakang setelah <body> diganti.
        (function() {
            'use strict';

            function tutupSocket() {
                window.__sentraChatAktif = false;

                if (window.__sentraChatReconnect) {
                    clearTimeout(window.__sentraChatReconnect);
                    window.__sentraChatReconnect = null;
                }

                if (window.__sentraChatSocket) {
                    try {
                        window.__sentraChatSocket.onclose = null;
                        window.__sentraChatSocket.close();
                    } catch (e) { /* socket sudah tertutup */ }
                    window.__sentraChatSocket = null;
                }
            }

            if (!window.__sentraChatTeardownBound) {
                window.__sentraChatTeardownBound = true;
                document.addEventListener('livewire:navigating', tutupSocket);
            }

            // Dipakai halaman chat ketika dipasang ulang untuk menutup socket
            // yang masih hidup dari kunjungan sebelumnya.
            window.__sentraChatTeardown = tutupSocket;
        })();

        // Bagian halaman: dijalankan setiap kali skrip ini dipasang (load awal
        // maupun setelah soft navigation).
        (function() {
            'use strict';

            if (!document.getElementById('conversation-list') || !document.getElementById('chat-panel')) {
                return; // bukan halaman chat
            }

            if (window.__sentraChatTeardown) {
                window.__sentraChatTeardown();
            }

            window.__sentraChatAktif = true;

            var adminId = {{ $adminId ?? 'null' }};
            var wsToken = @json($wsToken ?? '');
            var activeUserId = {{ isset($activeUser) ? (int) $activeUser->id_akun : 'null' }};
            var ws = null;
            var wsProtocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
            var wsHost = window.location.hostname || 'localhost';
            var wsPort = {{ env('WS_PORT', 3021) }};
            var chatFetchUrl = '{{ route('chat.fetch', ['userId' => '__USER__']) }}';
            var emptyChatHtml = '<div class="empty-state">' +
                '<div class="empty-icon"><i class="fa fa-comment-dots"></i></div>' +
                '<p class="mb-0">Belum ada pesan. Mulai percakapan di bawah.</p>' +
                '</div>';

            // Cache pesan seluruh percakapan dikirim bersama halaman. Klik
            // percakapan cukup merender cache ini → tidak ada request/tunda.
            var chatCache = {};
            (function() {
                var el = document.getElementById('chat-prefetch');
                if (!el) return;
                try {
                    chatCache = JSON.parse(el.textContent || '{}') || {};
                } catch (e) {
                    chatCache = {};
                }
            })();

            var replyToId = null;

            function normalizeChat(chat) {
                return {
                    id_chat: chat.id_chat || null,
                    sender_id: chat.sender_id,
                    receiver_id: chat.receiver_id,
                    message: chat.message || '',
                    replied_to_id: chat.replied_to_id || null,
                    replied_message: chat.replied_message || null,
                    sent_at: chat.sent_at || new Date().toISOString()
                };
            }

            // Cari teks pesan yang dibalas dari cache percakapan. Dipakai
            // ketika pesan baru datang lewat WebSocket (hanya membawa id).
            function findMessageById(id) {
                if (!id) return null;

                var key = String(id);

                for (var userId in chatCache) {
                    var list = chatCache[userId] || [];

                    for (var i = 0; i < list.length; i++) {
                        if (String(list[i].id_chat) === key) {
                            return list[i].message;
                        }
                    }
                }

                return null;
            }

            function setReply(id, text) {
                replyToId = id ? String(id) : null;

                var box = $('#chat-reply-preview');

                if (replyToId) {
                    box.removeClass('d-none');
                    box.find('.chat-reply-preview-text').text('Membalas: ' + (text || ''));
                    $('#chat-replied-to').val(replyToId);
                } else {
                    box.addClass('d-none');
                    box.find('.chat-reply-preview-text').text('');
                    $('#chat-replied-to').val('');
                }
            }

            function formatTime(value) {
                var raw = String(value || '').trim();
                var date = new Date(raw);

                if (isNaN(date.getTime()) && raw.indexOf(' ') !== -1) {
                    date = new Date(raw.replace(' ', 'T'));
                }

                if (isNaN(date.getTime())) return '';

                return date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function buildBubble(chat) {
                var isMe = String(chat.sender_id) === String(adminId);
                var bubble = $('<div>').addClass('chat-bubble' + (isMe ? ' is-me' : ''));

                if (chat.id_chat) {
                    bubble.attr('data-chat-id', chat.id_chat);
                }

                var quoted = chat.replied_message || findMessageById(chat.replied_to_id);

                if (quoted) {
                    $('<div>').addClass('chat-reply-quote').text(quoted).appendTo(bubble);
                }

                $('<p>').text(chat.message).appendTo(bubble);
                $('<span>').addClass('chat-bubble-time').text(formatTime(chat.sent_at)).appendTo(bubble);

                if (chat.id_chat) {
                    $('<button>')
                        .attr({
                            type: 'button',
                            'class': 'chat-reply-btn',
                            'data-reply-id': chat.id_chat,
                            'data-reply-text': chat.message
                        })
                        .text('Balas')
                        .appendTo(bubble);
                }

                return bubble;
            }

            function renderMessages(chats) {
                var container = $('#chat-messages');
                if (!container.length) return;

                container.empty();

                if (!chats || !chats.length) {
                    container.append(emptyChatHtml);
                } else {
                    chats.forEach(function(chat) {
                        container.append(buildBubble(chat));
                    });
                }

                container.scrollTop(container[0].scrollHeight);
            }

            function appendBubble(chat) {
                var container = $('#chat-messages');
                if (!container.length || container.hasClass('d-none')) return;

                if (chat.id_chat && container.find('.chat-bubble[data-chat-id="' + chat.id_chat + '"]').length) {
                    return;
                }

                container.append(buildBubble(chat));
                container.scrollTop(container[0].scrollHeight);
            }

            function cachePush(userId, chat) {
                var key = String(userId);
                var entry = normalizeChat(chat);
                var list = chatCache[key] || (chatCache[key] = []);

                if (entry.id_chat && list.some(function(existing) {
                        return existing.id_chat && String(existing.id_chat) === String(entry.id_chat);
                    })) {
                    return entry;
                }

                list.push(entry);
                return entry;
            }

            function otherUserId(data) {
                return String(data.sender_id) === String(adminId) ? data.receiver_id : data.sender_id;
            }

            // Gabungkan hasil server dengan cache lokal. Pesan WebSocket yang
            // tiba saat sinkronisasi berjalan tidak boleh hilang.
            function mergeChats(primary, extra) {
                var seen = {};
                var merged = [];

                function push(chat) {
                    var key = chat.id_chat ?
                        'id:' + chat.id_chat :
                        'tmp:' + chat.sender_id + '|' + chat.sent_at + '|' + chat.message;

                    if (seen[key]) return;
                    seen[key] = true;
                    merged.push(chat);
                }

                (primary || []).forEach(push);
                (extra || []).forEach(push);

                merged.sort(function(a, b) {
                    return new Date(a.sent_at).getTime() - new Date(b.sent_at).getTime();
                });

                return merged;
            }

            function sameMessages(a, b) {
                if (a.length !== b.length) return false;

                for (var i = 0; i < a.length; i++) {
                    var idA = a[i].id_chat ? String(a[i].id_chat) : '';
                    var idB = b[i].id_chat ? String(b[i].id_chat) : '';

                    if (idA || idB) {
                        if (idA !== idB) return false;
                    } else if (a[i].message !== b[i].message || a[i].sent_at !== b[i].sent_at) {
                        return false;
                    }
                }

                return true;
            }

            // Render percakapan dari cache: sinkron, tanpa menunggu server.
            function renderConversation(userId) {
                var key = String(userId);
                var item = $('.chat-item[data-user-id="' + key + '"]');
                var name = (item.find('.chat-item-name').text() || '').trim();

                $('.chat-item').removeClass('active');
                item.addClass('active');
                item.find('.chat-unread').remove();

                $('#chat-head-name').text(name || 'Percakapan');
                $('#chat-head-avatar').text((name || '?').charAt(0).toUpperCase());
                $('#chat-head').removeClass('d-none');
                $('#chat-receiver').val(key);
                $('#chat-input').val('');
                setReply(null, '');
                $('#chat-form').removeClass('d-none');
                $('#chat-placeholder').addClass('d-none');
                $('#chat-messages').removeClass('d-none');

                renderMessages(chatCache[key] || []);
            }

            // Sinkronisasi latar belakang: tandai sudah dibaca + ambil pesan
            // terbaru yang mungkin belum masuk lewat WebSocket.
            function refreshConversation(userId) {
                var url = chatFetchUrl.replace('__USER__', encodeURIComponent(userId));

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                }).then(function(response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                }).then(function(payload) {
                    if (!payload || !Array.isArray(payload.chats)) return;

                    var key = String(userId);
                    var previous = chatCache[key] || [];
                    var normalized = payload.chats.map(normalizeChat);
                    var merged = mergeChats(normalized, previous);

                    chatCache[key] = merged;

                    // Hanya render ulang bila memang ada perubahan, supaya
                    // hasil klik yang sudah instan tidak berkedip.
                    if (String(activeUserId) === key && !sameMessages(previous, merged)) {
                        renderMessages(merged);
                    }
                }).catch(function() {
                    // Cache preload tetap dipakai bila sinkronisasi gagal.
                });
            }

            function openConversation(userId, href) {
                if (!userId) return;

                activeUserId = String(userId);
                renderConversation(userId);

                if (href) {
                    try {
                        window.history.replaceState(window.history.state, '', href);
                    } catch (e) {}
                }

                refreshConversation(userId);
            }

            // Klik percakapan → instan (tanpa reload / tanpa soft navigation).
            $(document).off('click.sentraChatItem').on('click.sentraChatItem', '.chat-item[data-user-id]', function(e) {
                if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

                e.preventDefault();

                var userId = String($(this).attr('data-user-id') || '');
                if (!userId) return;

                openConversation(userId, this.getAttribute('href'));
            });

            function updateConversationList(data, otherId) {
                var item = $('.chat-item[data-user-id="' + otherId + '"]');

                if (item.length) {
                    item.find('.chat-item-preview').text(data.message);
                    item.find('.chat-item-time').text('Baru saja');

                    // Badge unread hanya untuk pesan masuk dari lawan bicara.
                    if (String(data.sender_id) !== String(adminId) && String(otherId) !== String(activeUserId)) {
                        var badge = item.find('.chat-unread');
                        if (badge.length) {
                            var count = parseInt(badge.text()) || 0;
                            badge.text(count + 1);
                        } else {
                            item.find('.chat-item-bottom').append('<span class="chat-unread">1</span>');
                        }
                    }

                    item.prependTo('#conversation-list');
                } else if (String(data.sender_id) !== String(adminId)) {
                    // Pengirim baru belum ada di daftar: segarkan area daftar
                    // secara parsial (tanpa reload halaman).
                    if (window.sentraLiveRefresh && window.sentraLiveRefresh()) {
                        return;
                    }
                    window.location.reload();
                }
            }

            function handleIncomingMessage(data) {
                var otherId = otherUserId(data);

                cachePush(otherId, data);

                if (String(activeUserId) === String(otherId)) {
                    appendBubble(normalizeChat(data));
                }

                updateConversationList(data, otherId);
            }

            function connectWebSocket() {
                try {
                    ws = new WebSocket(wsProtocol + wsHost + ':' + wsPort);
                    window.__sentraChatSocket = ws;

                    ws.onopen = function() {
                        console.log('Admin WebSocket connected');
                        if (adminId && wsToken) {
                            ws.send(JSON.stringify({
                                type: 'register',
                                token: wsToken,
                                user_id: String(adminId),
                                user_type: 'admin'
                            }));
                        }
                    };

                    ws.onmessage = function(event) {
                        try {
                            var data = JSON.parse(event.data);

                            if (data.type === 'new_message') {
                                handleIncomingMessage(data);
                            }
                        } catch (e) {
                            console.error('Error parsing WS message:', e);
                        }
                    };

                    ws.onclose = function() {
                        // Jangan menyambung ulang bila pengguna sudah pindah
                        // halaman (socket ditutup manual saat navigating).
                        if (!window.__sentraChatAktif) return;

                        console.log('WS disconnected, reconnecting in 3s...');
                        window.__sentraChatReconnect = setTimeout(connectWebSocket, 3000);
                    };

                    ws.onerror = function(err) {
                        console.error('WS error:', err);
                    };
                } catch (e) {
                    console.error('Failed to init WS:', e);
                }
            }

            connectWebSocket();

            // Tombol "Balas" pada tiap bubble dan pembatalannya.
            $(document).off('click.sentraChatReply').on('click.sentraChatReply', '.chat-reply-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                setReply($(this).data('reply-id'), $(this).data('reply-text'));
                $('#chat-input').focus();
            });

            $(document).off('click.sentraChatReplyCancel').on('click.sentraChatReplyCancel', '#chat-reply-cancel', function(e) {
                e.preventDefault();
                setReply(null, '');
            });

            // Delegasi ke document agar form tetap berfungsi setelah panel
            // diubah oleh render cache.
            $(document).off('submit.sentraChat').on('submit.sentraChat', '#chat-form', function(e) {
                e.preventDefault();
                var form = $(this);
                var input = $('#chat-input');
                var message = input.val().trim();
                if (!message || !activeUserId) return;

                var repliedToId = $('#chat-replied-to').val() || null;

                // Serialize dulu: jQuery melewati field yang disabled.
                var payload = form.serialize();

                input.prop('disabled', true);

                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({
                        type: 'send_message',
                        sender_id: adminId,
                        receiver_id: activeUserId,
                        message: message,
                        replied_to_id: repliedToId
                    }));
                    input.val('').prop('disabled', false).focus();
                    setReply(null, '');
                } else {
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: payload,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                var chat = response.chat || {};
                                handleIncomingMessage({
                                    id_chat: chat.id_chat || null,
                                    sender_id: adminId,
                                    receiver_id: activeUserId,
                                    message: message,
                                    replied_to_id: repliedToId,
                                    replied_message: chat.replied_message || null,
                                    sent_at: chat.sent_at || new Date().toISOString()
                                });
                                input.val('');
                                setReply(null, '');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 401 || xhr.status === 419) {
                                alert('Sesi login Anda sudah berakhir. Halaman akan dimuat ulang, silakan login kembali lalu kirim pesan.');
                                window.location.reload();
                                return;
                            }

                            var serverMessage = xhr.responseJSON && xhr.responseJSON.message;

                            if (xhr.status === 403) {
                                alert(serverMessage || 'Layanan konsultasi chat sedang dinonaktifkan.');
                                return;
                            }

                            if (xhr.status === 422) {
                                alert(serverMessage || 'Pesan tidak valid.');
                                return;
                            }

                            alert('Gagal mengirim pesan');
                        },
                        complete: function() {
                            input.prop('disabled', false).focus();
                        }
                    });
                }
            });
        })();
    </script>
@endpush
