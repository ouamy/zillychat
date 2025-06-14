<div class="p-6 lg:p-8 bg-white border-b border-gray-200 max-w-3xl mx-auto rounded-md shadow-md">

    <div id="chat-messages" class="h-96 overflow-y-auto border border-gray-300 rounded p-4 mb-4 bg-gray-50">
        <div class="mb-2"><strong>Alice:</strong> Hi there!</div>
        <div class="mb-2"><strong>Bob:</strong> Hello! How are you?</div>
    </div>

<form id="chat-form" class="flex" onsubmit="event.preventDefault(); sendMessage();">
    <input
        type="text"
        id="chat-input"
        placeholder="Type your message..."
        class="flex-grow border border-gray-300 rounded-l px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
    />
    <span
        id="emoji-button"
        class="bg-gray-200 px-3 border-t border-b border-gray-300 cursor-pointer hover:bg-gray-300 flex items-center"
        title="Insert emoji"
    >
        🙂
    </span>
    <button
        type="submit"
        class="bg-indigo-600 text-white px-4 py-2 rounded-r hover:bg-indigo-700"
    >
        Send
    </button>
</form>
</div>

<script src="https://cdn.jsdelivr.net/npm/emoji-button@2.2.2/dist/index.min.js"></script>

<script>
  let picker;

  window.addEventListener('load', () => {
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

    } else {
      console.error('EmojiButton not loaded');
    }
  });

  function sendMessage() {
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');

    if (input.value.trim() === '') return;

    const newMessage = document.createElement('div');
    newMessage.classList.add('mb-2');
    newMessage.innerHTML = `<strong>You:</strong> ${input.value}`;
    messages.appendChild(newMessage);

    messages.scrollTop = messages.scrollHeight;
    input.value = '';
  }
</script>
