import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

  // this gets called via the data-action attribute in the view
  copyLinkToClipboard() {
    const copyLinkButton = document.querySelector('.copy-link-button');
    const copiedLink = copyLinkButton.dataset.copyToClipboardUrl;
    navigator.clipboard.writeText(copiedLink)
      .then(() => {
        this.showFeedbackMessage();
      })
      .catch((error) => {
        console.error('Failed to copy link to clipboard', error);
      });
  }

  showFeedbackMessage() {
    const feedbackMessage = document.getElementById('copyLinkFeedbackMessage');
    feedbackMessage.style.opacity = '1';
    feedbackMessage.style.display = 'block';
    setTimeout(() => {
      feedbackMessage.style.opacity = '0';
      feedbackMessage.style.display = 'none';
    }, 2000); // Hide the message after 2 seconds (adjust as needed)
  }
}
