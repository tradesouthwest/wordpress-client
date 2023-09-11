/* data-date + 14 days */
const today = new Date();
const fourteenDaysFromNow = new Date(today.getDate() + 14);

const elements = document.querySelectorAll("a[data-date]");

for (const element of elements) {
  const dayNumber = parseInt(element.getAttribute("data-date"));

  if (dayNumber === fourteenDaysFromNow.getDate()) {
    element.classList.add("ui-datepicker-unselectable");
  }
} 
console.log(fourteenDaysFromNow); 
