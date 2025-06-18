<div class="flex flex-col h-[90vh] w-full max-w-full px-6 py-4 bg-white">

    <!-- Chat messages box -->
    <div id="chat-messages" class="flex-grow overflow-y-auto border border-gray-300 rounded p-4 mb-4 bg-gray-50 max-w-3xl mx-auto">
        @if (isset($messages) && $messages->count() > 0)
            @foreach ($messages as $message)
                <div class="mb-2">
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
  <!-- Emoji picker button -->
  <button
    type="button"
    id="emoji-button"
    class="flex items-center justify-center border border-gray-300 rounded px-3 py-2 hover:bg-gray-100 focus:outline-none"
    aria-label="Pick an emoji"
  >
    🙂
  </button>

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
let picker;
let lastMessageId = {{ $messages->last()?->id ?? 0 }};
const chatBox = document.getElementById('chat-messages');

function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}

window.addEventListener('load', () => {
    // Initialize emoji picker
    if (typeof EmojiButton !== 'undefined') {
        picker = new EmojiButton({
            position: 'auto',
            positionPriority: ['top', 'bottom']
        });

        const emojiBtn = document.getElementById('emoji-button');
        const input = document.getElementById('chat-input');

        picker.on('emoji', emoji => {
            const start = input.selectionStart;
            const end = input.selectionEnd;
            const text = input.value;

            input.value = text.slice(0, start) + emoji + text.slice(end);
            input.selectionStart = input.selectionEnd = start + emoji.length;
            input.focus();
        });

                emojiBtn.addEventListener('click', () => {
            if (picker.pickerVisible) {
                picker.hidePicker();
            } else {
                picker.showPicker(emojiBtn);
            }
        });
    }

    scrollToBottom(); // Scroll on initial load
});

function appendMessage(msg) {
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `<span class="text-xs text-gray-500 mr-2">${new Date(msg.created_at).toLocaleTimeString()}</span><strong>${msg.user.name}:</strong> ${msg.message}`;
    chatBox.appendChild(div);
    scrollToBottom(); // Always scroll when new message is added
}

setInterval(() => {
    fetch(`/chat/messages?lastMessageId=${lastMessageId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(msg => {
                appendMessage(msg);
                lastMessageId = msg.id;
            });
        });
}, 3000);
window.addEventListener('load', () => {
    setTimeout(scrollToBottom, 100); // slight delay to ensure DOM is ready
});

const csrfToken = document.querySelector('#chat-form input[name="_token"]').value;

window.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  const input = form.querySelector('input[name="message"]');
  const csrfTokenInput = form.querySelector('input[name="_token"]');
  const csrfToken = csrfTokenInput ? csrfTokenInput.value : '';

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const message = input.value.trim();
    if (!message) return;

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ message })
    })
    .then(res => {
      if (!res.ok) throw new Error('Network response was not ok');
      return res.json();
    })
    .then(data => {
      if (data.status === 'success') {
        input.value = '';
        // optionally update UI
      } else {
        alert('Failed to send message');
      }
    })
    .catch(() => alert('Error sending message'));
  });
});



</script>

