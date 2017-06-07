var baseUrl = '/admin';

    $(document).ready(function($) {
    $.extend( true, jQuery.fn, {
        filePreview: function( options ){
            var defaults = {};
            if( options ){
                $.extend( true, defaults, options );
            }
            $.each( this, function(){
                var $this = $( this );
                $this.bind('change', function( evt ){

                    var files = evt.target.files;
                    for (var i = 0, f; f = files[i]; i++) {
                        // Only process image files.
                        //if (!f.type.match('image.*')) {
                        //    continue;
                        //}

                        var reader = new FileReader();
                        reader.onload = (function(theFile) {
                            return function(e) {
                                $this.attr('src',e.target.result);
                            };
                        })(f);

                        reader.readAsDataURL(f);
                    }
                });
            });
        }
    });
});

var TableFormActions = {

    afterPost: null,
    
    getForm: function(formClass, id, afterPostParam) {
        var modal = $('#mainModal');
        modal.find('.modal-header').hide();
        modal.find('.modal-body').html('<div class="text-center"><img src="/img/loader.gif"></div>');
        modal.find('.modal-footer').hide();
        modal.modal();
        $.getJSON(baseUrl + '/backend/' + formClass + '-form/get/', { id: id }, function (data) {
            modal.find('.modal-title').html(data.title);
            modal.find('.modal-header').show();
            modal.find('.modal-body').html(data.form);
            modal.find('.modal-footer').html(data.buttons);
            modal.find('.modal-footer').show();
        });
        if (afterPostParam) { TableFormActions.afterPost = afterPostParam; }
        return false;
    },
/*    
    getFiles: function(formClass){
        files = "&" + formClass +"%5Bfiles%5D=";
        filesData = {};
        i = 0;
        var reader = new FileReader();

        $('#' + formClass + ' .fileinput').each(function(indx, element){
            //собрать массив для фоток
            i++;
            var file_data = {};
            /*
             img = $(element).children("div").children("img");
             //chkbx = $(element).children("div").children("label").children("input[type=checkbox]");
             is_preview = 0;
             is_logo = 0;
             $(element).children("div").children("label").children("input[type=checkbox]").each(function(indx_c, element_c){
             if($(element_c).attr("name").indexOf("is_preview")+1){
             if ($(element_c).prop("checked")) is_preview = 1;				
             }
             else if($(element_c).attr("name").indexOf("is_logo")+1){
             if ($(element_c).prop("checked")) is_logo = 1;
             }
             });
             photo_data["id_photo"] = $(img).attr("id_photo") === undefined ? 0 : $(img).attr("id_photo");
             photo_data["is_logo"] = is_logo;
             photo_data["is_preview"] = is_preview;
             photo_data["file_bin"] = $(img).attr("src") === undefined ? "" : $(img).attr("src");
             */
//            alert($(element).attr("value"));
            //file_data["file_bin"] = $(element).attr("src") === undefined ? "" : $(element).attr("src");

    /*        
            reader.onload = function(event) {
                return JSON.stringify(event.target.result);
                //console.log(JSON.stringify(filesData))
            };
            
            filesData[i] =  reader.readAsBinaryString($(element)[0].files[0]);
        });
        
        
        return filesData;
    },
  */
    postFormClassic: function(formClass, afterPostParam) {
        $('#mainModal .btn').attr('disabled','disabled');

        var data = new FormData($('form#' + formClass)[0]);

        var params_string = '?';

        for (var key in afterPostParam) {
            var val = afterPostParam[key];
            params_string = params_string + key + '=' + val + '&';
        }

        $.ajax({
            url: baseUrl + '/backend/' + formClass + '-form/post/' + params_string,
            type: 'POST',
            data: data,
            success: function(answer) {
                answer = JSON.parse(answer);
                
                $('#mainModal .btn').removeAttr('disabled');
                if (answer.result == 'error') {
                    $('form#' + formClass + ' .has-error').removeClass('has-error');
                    $('form#' + formClass + ' .panel-danger').removeClass('panel-danger');
                    for (k in answer.fields) {
                        $('form#' + formClass + ' #form_group_' + k).addClass('has-error');
                        $('form#' + formClass + ' #form_group_' + k + ' .panel').addClass('panel-danger');
                    }
                }
                else {
                    TableFormActions.closeForm(formClass);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    },


    postForm: function(formClass, afterPostParam) {
        $('#mainModal .btn').attr('disabled','disabled');

        //base64 source code
        photos = "&"+ formClass +"%5Bphotos%5D=";
        photosData = {};
        i = 0;
        $('.fileinput.fileinput-exists').each(function(indx, element){
            //собрать массив для фоток
            i++;
            photo_data = {};

            img = $(element).children("div").children("img");
            //chkbx = $(element).children("div").children("label").children("input[type=checkbox]");
            is_preview = 0;
            is_logo = 0;
            $(element).children("div").children("label").children("input[type=checkbox]").each(function(indx_c, element_c){
                if($(element_c).attr("name").indexOf("is_preview")+1){
                    if ($(element_c).prop("checked")) is_preview = 1;
                }
                else if($(element_c).attr("name").indexOf("is_logo")+1){
                    if ($(element_c).prop("checked")) is_logo = 1;
                }
            });
            photo_data["id_photo"] = $(img).attr("id_photo") === undefined ? 0 : $(img).attr("id_photo");
            photo_data["is_logo"] = is_logo;
            photo_data["is_preview"] = is_preview;
            photo_data["file_bin"] = $(img).attr("src") === undefined ? "" : $(img).attr("src");

            //console.log(photo_data);
            
            photosData["photo" + i] = photo_data;
        });

        //собираем JSON из массива
        photos = photos + JSON.stringify(photosData);

        //base64 source code
/*
        files = "&"+ formClass +"%5Bfiles%5D=";
         
        filesData = {};
        i = 0;
        $('.file-preview').each(function(indx, element){
            i++;
            file_data = {};

            file = $(element);
            file_data["file_bin"] = $(file).attr("src") === undefined ? "" : $(file).attr("src");

            filesData["file" + i] = file_data;
        });

        //собираем JSON из массива
        files = files + JSON.stringify(filesData);
*/
        var params_string = '?';
        
        for (var key in afterPostParam) {
            var val = afterPostParam[key];
            params_string = params_string + key + '=' + val + '&';
        }
        
        $.ajax({
            url: baseUrl + '/backend/' + formClass + '-form/post/' + params_string,
            type: 'POST',
            data:
            {
                formdata: $('form#' + formClass).serialize() + photos,
                formkey: TableFormActions.getFormKey(formClass)
            },
            success: function(answer) {
                $('#mainModal .btn').removeAttr('disabled');
                if (answer.result == 'error') {
                    $('form#' + formClass + ' .has-error').removeClass('has-error');
                    $('form#' + formClass + ' .panel-danger').removeClass('panel-danger');
                    for (k in answer.fields) {
                        $('form#' + formClass + ' #form_group_' + k).addClass('has-error');
                        $('form#' + formClass + ' #form_group_' + k + ' .panel').addClass('panel-danger');
                    }
                } 
                else {
                    TableFormActions.closeForm(formClass);
                }
            },
            dataType: 'json'
        });
        return false;
    },

    //сначала мы аплоадим картинку, потом получаем
    uploadImagePostForm: function(formClass){
        $('#fileupload').fileupload({
            url: '/path/to/upload/handler.json',
            sequentialUploads: true
        });
        
    },
    
    closeForm: function(formClass) {
        console.log();
        if (TableFormActions.afterPost) {
            TableFormActions.afterPost(formClass);
        } else {
            TableFormActions.reloadTable(formClass);
        }
        TableFormActions.afterPost = null;
    },

    reloadTable: function(formClass) {
        $('#mainModal').modal('hide');
        $.get(baseUrl + '/backend/' + formClass + '-table/', function (data) {
            $('.info-content').html(data);
        });					
    },
    
    deleteConfirmObject: function(formClass, id, afterPostParam){
        var modal = $('#mainModal');
        modal.find('.modal-header').hide();
        modal.find('.modal-body').html('<div class="text-center"><img src="/img/loader.gif"></div>');
        modal.find('.modal-footer').hide();
        modal.modal();
        $.getJSON(baseUrl + '/backend/' + formClass + '-form/delete-confirm/', { id: id }, function (data) {
            modal.find('.modal-title').html(data.title);
            modal.find('.modal-header').show();
            modal.find('.modal-body').html(data.form);
            modal.find('.modal-footer').html(data.buttons);
            modal.find('.modal-footer').show();
        });
        if (afterPostParam) { TableFormActions.afterPost = afterPostParam; }
        return false;
    },
    
    deleteObject: function(formClass) {
        $.ajax({
            url: baseUrl + '/backend/' + formClass + '-object/delete/',
            type: 'POST',
            data: { formkey: TableFormActions.getFormKey(formClass)/*,  formdata: $('form#' + formClass).serialize() */},
            success: function(answer) { TableFormActions.reloadTable(formClass); },
            dataType: 'json'
        });
    },
    
    restoreObject: function(formClass) {
        $.ajax({
            url: baseUrl + '/backend/' + formClass + '-object/restore/',
            type: 'POST',
            data: { formkey: TableFormActions.getFormKey(formClass) },
            success: function(answer) { TableFormActions.reloadTable(formClass); },
            dataType: 'json'
        });
    },
    
    getFormKey: function(formClass) {
        var formkey = {};
        $('form#' + formClass + ' .form-control:disabled, form#' + formClass + ' .form-control[type="hidden"]').each(function () {
            formkey[$(this).attr('name').match(/\[([^\]]+)\]/)[1]] = $(this).val();
        });
        return formkey;
    }
}

var OpeningTime = {
    addRow: function () {
        var id = $(this).attr('target-panel');
        var new_row = $('#' + id + ' tr.new-input-list-row').clone();//будующая новая невидимая строка
        var row_num = Number(new_row.attr("row-num")); //номер текущей новой строки
        var row_num_new = row_num + 1; //номер новой строки

        $('#' + id + ' tr.new-input-list-row').css("display", "table-row").removeClass("new-input-list-row");

        //новая невидимая строка
        new_row.insertBefore($("#" + id + " .input-list-add-row").parent().parent("tr"));
        $('#' + id + ' tr.new-input-list-row').attr("row-num", row_num_new);
        //меняем ID и имя всех контролов внутри новой строки
        $('#' + id + ' tr.new-input-list-row').find('select, input, label').map(function () {
            var id = $(this).attr("id");
            var name = $(this).attr("name");
            var label_for = $(this).attr("for");
            
            if(id != undefined){
                id = id.replace('_' + row_num, '_' + row_num_new);
                $(this).attr("id", id);
            }
            
            if(name != undefined){
                name = name.replace('[' + row_num + ']', '[' + row_num_new + ']');
                $(this).attr("name", name);
            }
            
            if (label_for != undefined) {
                label_for = label_for.replace('_' + row_num, '_' + row_num_new);
                $(this).attr("for", label_for);
            }
        });
        OpeningTime.firstCheckStatus(id);
    },
    deleteRow: function(){
        var id = $(this).attr('target-panel');
        $(this).parent().parent("tr").remove();
        OpeningTime.firstCheckStatus(id);
    },
    firstCheckStatus: function(id) {
        var cntTotal = $('#' + id + ' tr').size() - 2;
        if(cntTotal < 0) cntTotal = 0;
        $('#' + id + ' .panel-title .badge').html(cntTotal);
    }
}