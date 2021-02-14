jQuery(document).ready(function($){
	$('.custom-phone-field input').on('keyup', function(e){
		let numbers = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
		let value = this.value;
		if (numbers.includes(e.key)) {
			if(this.value.length > this.maxLength){
				$(this).val(value.slice(0, -1));
			} else {
				let replaceValue = value.slice(0, -1) + e.key;
				if (!(replaceValue.length > this.maxLength)){
					$(this).val();
				}
			}
		} else if(e.key === "Backspace"){
			if(this.value.length === 0){
				let prev = $(this).closest('.custom-phone-field').prev('.custom-phone-field');
				if (prev.length)
					$(this).closest('.custom-phone-field').prev('.custom-phone-field').find('input').focus();
			}
		}else {
			let newValue = value.slice(0, -1);
			$(this).val(newValue);
		}

		if (this.value.length === this.maxLength) {
			let next = $(this).closest('.custom-phone-field').next('.custom-phone-field');
			if (next.length)
				$(this).closest('.custom-phone-field').next('.custom-phone-field').find('input').focus();
		}
	});
});