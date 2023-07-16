import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.listenForNotificationSettingChange();
  }

  listenForNotificationSettingChange() {
    if (typeof notificationToggle === 'undefined') {
      const notificationToggle = document.getElementById('add_player_to_group_wantsNotifications');
      const emailInputContainer = document.getElementById('email-input-container');
      const emailInput = document.getElementById('add_player_to_group_email');

      notificationToggle.addEventListener('change', function () {
        if (notificationToggle.checked) {
          emailInputContainer.style.display = 'block';
          emailInput.setAttribute('required', 'true');
          emailInput.removeAttribute('disabled');
        } else {
          emailInputContainer.style.display = 'none';
          emailInput.removeAttribute('required');
          emailInput.setAttribute('disabled', 'disabled');
        }
      });
    }
  }
}
