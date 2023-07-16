import {Controller} from '@hotwired/stimulus';
import flatpickr from 'flatpickr';

export default class extends Controller {
  connect() {
    this.initDatePickers();
  }

  initDatePickers() {
    const flatpickrAlreadyExists = document.querySelector('.flatpickr-input');

    if (flatpickrAlreadyExists) {
      return;
    }

    const datepickerElements = document.querySelectorAll(".js-datepicker");

    datepickerElements.forEach(element => {
      flatpickr(element, {
        dateFormat: "Y-m-d H:i",
        altInput: true,
        altFormat: "l, j. F Y, H:i",
        enableTime: true,
        minDate: "today",
        time_24hr: true,
        allowInput: true,
      });
    });
  }
}
