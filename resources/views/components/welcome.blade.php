<div class="flex flex-col h-[90vh] w-full max-w-full px-6 py-4 bg-white">

    <!-- Chat messages box -->
    <div id="chat-messages" class="flex-grow overflow-y-auto border border-gray-300 rounded p-4 mb-4 bg-gray-50 max-w-3xl mx-auto">
        @if (isset($messages) && $messages->count() > 0)
            @foreach ($messages as $message)
                <div class="mb-2" data-message-id="{{ $message->id }}">
                    <span class="text-xs text-gray-500 mr-2">
                        @if ($message->created_at)
                            @php
                                $diffInMinutes = $message->created_at->diffInMinutes(now());
                            @endphp

                            @if ($diffInMinutes <= 60)
                                {{ $message->created_at->diffForHumans() }}
                            @else
                                {{ $message->created_at->format('d/m/Y H:i') }}
                            @endif
                        @else
                            {{-- No timestamp --}}
                            From Old ZillyChat
                        @endif
                    </span>
                    <strong>{{ $message->user->name }}:</strong> {{ $message->message }}
                </div>
            @endforeach
        @else
            <div class="mb-2"><em>No messages yet</em></div>
        @endif
    </div>

    <!-- Chat form -->
    <form method="POST" action="/chat/send" id="chat-form" class="flex w-full max-w-3xl mx-auto gap-2 items-center">
        @csrf
        <input
            id="chat-input"
            name="message"
            type="text"
            placeholder="Type your message..."
            class="flex-grow border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-400"
        />
        <button
            type="button"
            id="emoji-button"
            class="flex items-center justify-center border border-gray-300 rounded px-3 py-2 hover:bg-gray-100 focus:outline-none"
            aria-label="Pick an emoji"
        >🙂</button>
        <button
            type="submit"
            style="background-color:#2563eb; color:white; padding:0.5rem 1rem; border-radius:0.375rem;"
        >
            Send
        </button>
    </form>

</div>

<!-- Emoji Picker -->
<script src="https://cdn.jsdelivr.net/npm/emoji-button@2.2.2/dist/index.min.js"></script>

<script>
  const form = document.getElementById('chat-form');
  const input = document.getElementById('chat-input');
  const chatBox = document.getElementById('chat-messages');
  const emojiBtn = document.getElementById('emoji-button');
  const csrfToken = document.querySelector('input[name="_token"]').value;

  // Get last message ID from rendered messages or 0 if none
  let lastMessageId = (() => {
    const messages = chatBox.querySelectorAll('[data-message-id]');
    if (messages.length === 0) return 0;
    return parseInt(messages[messages.length - 1].getAttribute('data-message-id'));
  })();

  // Format timestamp similar to Blade
  function formatTimestamp(dateString) {
    const createdAt = new Date(dateString);
    const now = new Date();
    const diffMs = now - createdAt;
    const diffMins = diffMs / 1000 / 60;

    if (diffMins <= 60) {
      const minutesAgo = Math.floor(diffMins);
      return minutesAgo === 0 ? 'just now' : `${minutesAgo} minute${minutesAgo > 1 ? 's' : ''} ago`;
    } else {
      return createdAt.toLocaleString('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
      });
    }
  }

  // Append message
  function appendMessage(msg) {
    if (chatBox.querySelector(`[data-message-id="${msg.id}"]`)) return;

    const div = document.createElement('div');
    div.className = 'mb-2';
    div.setAttribute('data-message-id', msg.id);

    div.innerHTML = `<span class="text-xs text-gray-500 mr-2">${formatTimestamp(msg.created_at)}</span><strong>${msg.user.name}:</strong> ${msg.message}`;

    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  // Handle form submit
  form.addEventListener('submit', e => {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ message }),
      credentials: 'same-origin',
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        input.value = '';
        appendMessage(data.message);
        lastMessageId = data.message.id;
      } else {
        alert('Failed to send message');
      }
    })
    .catch(() => alert('Network error'));
  });

  // Poll for new messages every 0.5s
  setInterval(() => {
    fetch(`/chat/messages?lastMessageId=${lastMessageId}`)
      .then(res => res.json())
      .then(messages => {
        messages.forEach(msg => {
          appendMessage(msg);
          lastMessageId = Math.max(lastMessageId, msg.id);
        });
      })
      .catch(() => {
        console.error('Failed to fetch new messages');
      });
  }, 500);

  // Scroll to bottom on load
  window.addEventListener('load', () => {
    chatBox.scrollTop = chatBox.scrollHeight;
  });

  
  window.addEventListener('DOMContentLoaded', () => {
    const picker = new EmojiButton({
      position: 'top-start'
    });

    picker.on('emoji', emoji => {
      const start = input.selectionStart;
      const end = input.selectionEnd;
      const text = input.value;
      input.value = text.slice(0, start) + emoji + text.slice(end);
      input.selectionStart = input.selectionEnd = start + emoji.length;
      input.focus();
    });

    let pickerVisible = false;

emojiBtn.addEventListener('click', () => {
  if (pickerVisible) {
    picker.hidePicker();
  } else {
    picker.showPicker(emojiBtn);
  }
  pickerVisible = !pickerVisible;
});

  });
</script>

