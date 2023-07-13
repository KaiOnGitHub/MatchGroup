import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.scrollChatToBottom();
    this.focusChatInput();
    this.onNewMessage();
  }

  onNewMessage() {
    const url = JSON.parse(document.getElementById("chat-mercure-url").textContent);
    const eventSource = new EventSource(url);

    eventSource.onmessage = event => {
      this.hideNotificationsAfterTimeout();
    }
  }

  scrollChatToBottom() {
    const chatContainer = document.querySelector(".chat-container");
    chatContainer.scrollTop = chatContainer.scrollHeight;
  }

  focusChatInput() {
    const chatInput = document.querySelector("#add_message_message");
    chatInput.focus();
  }

  /*
   * Called via data-action attribute
   */
  submitFormOnEnter(event) {
    if (event.which === 13 && !event.shiftKey) {
      if (!event.repeat) {
        const form = event.target.form;
        form.submit();
      }

      event.preventDefault(); // Prevents the addition of a new line in the text field
    }
  }

}
