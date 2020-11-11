/* version: 1 */
jQuery(document).ready(function () {
	$("#MpprSelect").change(function (){   
    var optionText = $("#MpprSelect option:selected").val();
        
    $('#MpprText').html(msg);  
        var msg = ("Option: "+optionText).text();
});
});