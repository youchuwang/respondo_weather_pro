function schedule_delete( zipcode ) {
	if( confirm( 'Do you want to delte this schedule?' ) ){
		document.getElementById('deletezipcode').value = zipcode;
		
		document.getElementById('deleteform').submit();
	}
}

function submitform(){
	document.submit_zipcode.submit();
}