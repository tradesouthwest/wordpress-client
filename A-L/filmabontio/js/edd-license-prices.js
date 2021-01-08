/* requires cookie.js https://github.com/UnSstrennen/cookie.js */


var cValue = '';
var expire = (new Date(Date.now()+ 86400*1000)).toUTCString();
/*
 * setCookie('elp_license_prices', cValue, {expires: expire });  // string cookie
 * setCookie('elp_license_prices', {key: cValue}, {expires: expire });  // json cookie
 * getCookie('name')  // string cookies
 * getCookie('name', true)  // json cookie, returns json object
 * deleteCookie('name');  // json or string cookie: no matter!
 */