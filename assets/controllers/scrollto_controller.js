import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.scrollToTarget();
  }

  scrollToTarget() {
    const scrollTargetContainer = document.querySelector("[data-scroll-target]");
    const scrollTargetSelector = scrollTargetContainer.dataset.scrollTarget;

    if (scrollTargetSelector) {
      const scrollToElement = document.querySelector(scrollTargetSelector);
      const rect = scrollToElement.getBoundingClientRect();
      const scrollTop = document.documentElement.scrollTop;
      setTimeout(() => {
        window.scrollTo(0, rect.top + scrollTop - 100)
      }, 50);
    }
  }
}
