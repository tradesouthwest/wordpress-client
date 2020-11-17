/**
 *
 * This program is free software. You are allowed to use the software but NOT allowed to modify the software.
 * It is also not legal to do any changes to the software and distribute it in your own name / brand.
 */
 
var EbsnextOnlineClassicPaymentWindow = EbsnextOnlineClassicPaymentWindow ||
(function() {
	var _epayArgsJson = {};
	return {
		init: function (epayArgsJson) {
			_epayArgsJson = epayArgsJson;
		},
		getJsonData: function() {
			return _epayArgsJson;
		},
	}
}());

var isPaymentWindowReady = false;
var timerOpenWindow;

function PaymentWindowReady() {
	paymentwindow = new PaymentWindow(EbsnextOnlineClassicPaymentWindow.getJsonData());

	isPaymentWindowReady = true;
}
function openPaymentWindow() {
	if (isPaymentWindowReady) {
		clearInterval(timerOpenWindow);
		paymentwindow.open();
	}
}

document.addEventListener('readystatechange', function (event) {
    if (event.target.readyState === "complete") {
        timerOpenWindow = setInterval("openPaymentWindow()", 500);
    }
});