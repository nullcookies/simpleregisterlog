var watchValue;

var SelectFromMultipleDb = {

	addElement: function(id, backendUrl) {
		var newElement = $('#' + id + '_chosen .no-results span').html();
		if (newElement) {
			$.getJSON(backendUrl, { element: newElement }, function(data) {
				if (data.id) {
					var option = $('<option value="' + data.id + '" selected="selected">' + data.element + '</option>');
					$('#' + id).append(option);
					$('#' + id).trigger('chosen:updated');
				}
			});
		}
		return false;
	}

}


/**
 * методы для виджета nomvcCheckboxFromDbWidget
 */
var CheckboxFromDb = {
	expandPanel: function () {
		var pan = $(this).parent().find('.panel-body');
		if (pan.is(':visible')) {
			pan.hide();
		} else {
			pan.show();
		}
	},
	
	clickSelectAll: function() {
		var id = $(this).attr('target-panel');
		$('#' + id + ' input.checkbox-element').prop('checked', this.checked);
		CheckboxFromDb.firstCheckStatus(id);
	},
	
	checkStatus: function() {
		var id = $(this).attr('target-panel');
		CheckboxFromDb.firstCheckStatus(id);
	},
	
	firstCheckStatus: function(id) {
		var cntTotal = $('#' + id + ' input.checkbox-element').size();
		var cntSelected = $('#' + id + ' input:checked.checkbox-element').size();
		$('#' + id + ' input.checkbox-all').prop('checked', cntSelected == cntTotal);
		$('#' + id + ' .panel-title .badge').html(cntSelected + ' / ' + cntTotal);
	}

};

var BatchActions = {
	clickSelectAll: function() {
		$('input.batch_select_row').prop('checked', this.checked);
		BatchActions.firstCheckStatus();
	},
	
	checkStatus: function() {
		BatchActions.firstCheckStatus();
	},
	
	firstCheckStatus: function() {
		var cntTotal = $('input.batch_select_row').size();
		var cntSelected = $('input:checked.batch_select_row').size();
		$('input.batch_select_all').prop('checked', cntSelected == cntTotal);
	},
	
	getIds: function() {
		var data = [];
		$('input:checked.batch_select_row').each(function () {
			data.push($(this).closest('tr').attr('row-id'));
		});
		return data;
	}

};

/** 
 * Методы для виджета nomvcParametersListWidget
 */
var ParametersList = {
	expandPanel: function () {
		var pan = $(this).parent().find('.panel-body');
		if (pan.is(':visible')) {
			pan.hide();
		} else {
			pan.show();
		}
	},
	clickSelectAll: function () {
		var id = $(this).attr('target-panel');
		$('#' + id + ' input.checkbox-element').prop('checked', this.checked);
		ParametersList.firstCheckStatus(id);
	},
	checkStatus: function () {
		var id = $(this).attr('target-panel');
		ParametersList.firstCheckStatus(id);
	},
	firstCheckStatus: function (id) {
		var cntTotal = $('#' + id + ' input.checkbox-element').size();
		var cntSelected = $('#' + id + ' input:checked.checkbox-element').size();
		$('#' + id + ' input.checkbox-all').prop('checked', cntSelected == cntTotal);
		$('#' + id + ' .panel-title .badge').html(cntSelected + ' / ' + cntTotal);
	},
	checkParameter: function(event){
		name = $(event.target).attr("name").replace(/\[val\]/g,"[on]");//имя checkbox-а
		checkbox = $('[name ="'+ name +'"]');
		value = String($(event.target).val()); // значение инпута
		
		if(value.length > 0){
			$(checkbox).prop('checked', true);
		}
		else{
			$(checkbox).prop('checked', false);
		}
		ParametersList.firstCheckStatus($(checkbox).attr('target-panel'));
	},
	clearInput: function(name){
		$('[name = "' + name +'"]').val("");
	}
}

/** 
 * Методы для виджета nomvcInputListWidget
 */
var InputList = {
	expandPanel: function () {
		var pan = $(this).parent().find('.panel-body');
		if (pan.is(':visible')) {
			pan.hide();
		} else {
			pan.show();
		}
	},
	checkStatus: function () {
		var id = $(this).attr('target-panel');
		InputList.firstCheckStatus(id);
	},
	addRow: function(){
		var id = $(this).attr('target-panel');
		var new_row = $('#' + id + ' tr.new-input-list-row').clone();//будующая новая невидимая строка
		var row_num = Number(new_row.attr("row-num")); //номер текущей новой строки
		var row_num_new = row_num + 1; //номер новой строки
		
		$('#' + id + ' tr.new-input-list-row').css("display", "table-row").removeClass("new-input-list-row");
		
		//новая невидимая строка
		new_row.insertBefore($("#" + id + " .input-list-add-row").parent().parent("tr"));
		$('#' + id + ' tr.new-input-list-row').attr("row-num", row_num_new);
		//меняем ID и имя всех контролов внутри новой строки
		$('#' + id + ' tr.new-input-list-row input').map(function(){
			var id = $(this).attr("id");
			var name = $(this).attr("name");
			
			id = id.replace('_' + row_num, '_' + row_num_new);
			$(this).attr("id", id);
			
			name = name.replace('['+row_num+']', '['+row_num_new+']');
			$(this).attr("name", name);
		});
		InputList.firstCheckStatus(id);
	},
	deleteRow: function(){
		var id = $(this).attr('target-panel');
		$(this).parent().parent("tr").remove();
		InputList.firstCheckStatus(id);
	},
	firstCheckStatus: function(id) {
		var cntTotal = $('#' + id + ' input.checkbox-element').size() - 1;
		var cntSelected = $('#' + id + ' input:checked.checkbox-element').size();
		$('#' + id + ' input.checkbox-all').prop('checked', cntSelected == cntTotal);
		$('#' + id + ' .panel-title .badge').html(cntSelected + ' / ' + cntTotal);
	}
}
