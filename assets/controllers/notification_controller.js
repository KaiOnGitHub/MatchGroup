import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.onNewNotification();
    this.hideNotificationsAfterTimeout();
  }

  onNewNotification() {
    const url = JSON.parse(document.getElementById("notifications-mercure-url").textContent);
    const eventSource = new EventSource(url);

    eventSource.onmessage = event => {
      this.hideNotificationsAfterTimeout();
    }
  }

  hideNotificationsAfterTimeout() {
    const notifactionCenter = document.querySelector('.notification-center');
    const notifications = notifactionCenter.querySelectorAll('.notification');
    notifications.forEach((notification) => {
      setTimeout(() => {
        notification.remove();
      }, 5000);
    });
  }


}
